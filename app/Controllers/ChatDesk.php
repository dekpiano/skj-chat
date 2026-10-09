<?php

namespace App\Controllers;

use App\Models\ChatSessionModel;
use App\Models\ChatMessageModel;
use App\Models\AgentModel;
use App\Models\CannedReplyModel;

class ChatDesk extends BaseController
{
    public function index()
    {
        if ($redir = $this->checkAuth()) {
            return $redir;
        }

        $cannedModel = new CannedReplyModel();
        $cannedReplies = $cannedModel->orderBy('shortcut', 'ASC')->findAll();

        $agentModel = new AgentModel();
        $agents = $agentModel->where('status', 'active')->findAll();

        $aiConfig = $this->db->table('tb_chat_ai_config')->where('ai_id', 1)->get()->getRow();

        $data = [
            'title'         => 'ศูนย์ควบคุมการสนทนาสด (Live Chat Desk)',
            'activeMenu'    => 'chat_desk',
            'cannedReplies' => $cannedReplies,
            'agents'        => $agents,
            'aiConfig'      => $aiConfig,
            'activeSession' => $this->request->getGet('session') ?? '',
        ];

        return view('chat/desk', array_merge($this->data, $data));
    }

    public function getQueue()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $filter = $this->request->getGet('filter') ?? 'active'; // active, unassigned, mine, closed, all
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->db->table('tb_chat_sessions s')
            ->select('s.*, a.fullname as assigned_agent_name, a.avatar as assigned_agent_avatar')
            ->join('tb_chat_agents a', 'a.agent_id = s.assigned_agent_id', 'left');

        if ($filter === 'active') {
            $builder->where('s.status', 'active');
        } elseif ($filter === 'unassigned') {
            $builder->where('s.status', 'active')->where('s.assigned_agent_id IS NULL');
        } elseif ($filter === 'mine') {
            $builder->where('s.status', 'active')->where('s.assigned_agent_id', $this->currentAgent->agent_id);
        } elseif ($filter === 'closed') {
            $builder->where('s.status', 'closed');
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('s.user_name', $search)
                ->orLike('s.user_tel', $search)
                ->orLike('s.notes', $search)
                ->groupEnd();
        }

        $builder->orderBy('s.updated_at', 'DESC');
        $sessions = $builder->get()->getResult();

        foreach ($sessions as &$s) {
            $lastMsg = $this->db->table('tb_chat_messages')
                ->where('session_id', $s->session_id)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();

            $s->last_message = $lastMsg ? $lastMsg->message : '';
            $s->last_attachment = $lastMsg ? $lastMsg->attachment_url : null;
            $s->last_sender = $lastMsg ? $lastMsg->sender_type : '';
            $s->last_message_time = $lastMsg ? $lastMsg->created_at : $s->updated_at;
        }

        // Stats summary for tab badges
        $stats = [
            'active'     => $this->db->table('tb_chat_sessions')->where('status', 'active')->countAllResults(),
            'unassigned' => $this->db->table('tb_chat_sessions')->where('status', 'active')->where('assigned_agent_id IS NULL')->countAllResults(),
            'mine'       => $this->db->table('tb_chat_sessions')->where('status', 'active')->where('assigned_agent_id', $this->currentAgent->agent_id)->countAllResults(),
            'closed'     => $this->db->table('tb_chat_sessions')->where('status', 'closed')->countAllResults(),
        ];

        return $this->response->setJSON([
            'status'   => 'success',
            'sessions' => $sessions,
            'stats'    => $stats,
        ]);
    }

    public function getSessionDetail($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $session = $this->db->table('tb_chat_sessions s')
            ->select('s.*, a.fullname as assigned_agent_name, a.avatar as assigned_agent_avatar')
            ->join('tb_chat_agents a', 'a.agent_id = s.assigned_agent_id', 'left')
            ->where('s.session_id', $sessionId)
            ->get()
            ->getRow();

        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลการสนทนา']);
        }

        // Mark admin unread count to 0 and mark user messages as read
        $this->db->table('tb_chat_messages')
            ->where('session_id', $sessionId)
            ->where('sender_type', 'user')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $this->db->table('tb_chat_sessions')
            ->where('session_id', $sessionId)
            ->update([
                'unread_admin_count' => 0,
                'admin_active_at'    => date('Y-m-d H:i:s'),
            ]);

        $afterId = (int)($this->request->getGet('after_id') ?? 0);

        $msgBuilder = $this->db->table('tb_chat_messages m')
            ->select('m.*, ag.fullname as agent_fullname, ag.avatar as agent_avatar')
            ->join('tb_chat_agents ag', 'ag.agent_id = m.agent_id', 'left')
            ->where('m.session_id', $sessionId);

        if ($afterId > 0) {
            $msgBuilder->where('m.message_id >', $afterId);
        }

        $messages = $msgBuilder->orderBy('m.created_at', 'ASC')
            ->get()
            ->getResult();

        return $this->response->setJSON([
            'status'   => 'success',
            'session'  => $session,
            'messages' => $messages,
        ]);
    }

    public function sendMessage()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $sessionId = (int)$this->request->getPost('session_id');
        $message   = trim($this->request->getPost('message') ?? '');
        $attachmentUrl  = $this->request->getPost('attachment_url') ?? null;
        $attachmentType = $this->request->getPost('attachment_type') ?? null;

        if (empty($message) && empty($attachmentUrl)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อความหรือเลือกไฟล์']);
        }

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลเซสชัน']);
        }

        $now = date('Y-m-d H:i:s');

        // Insert admin reply
        $this->db->table('tb_chat_messages')->insert([
            'session_id'      => $sessionId,
            'sender_type'     => 'admin',
            'sender_name'     => $this->currentAgent->fullname,
            'agent_id'        => $this->currentAgent->agent_id,
            'message'         => $message,
            'attachment_url'  => $attachmentUrl,
            'attachment_type' => $attachmentType,
            'is_bot'          => 0,
            'is_read'         => 0,
            'created_at'      => $now
        ]);
        $msgId = $this->db->insertID();

        // Update session
        $updateSession = [
            'updated_at'          => $now,
            'last_admin_reply_at' => $now,
            'admin_active_at'     => $now,
            'unread_user_count'   => $session->unread_user_count + 1,
            'is_bot_paused'       => 1 // automatically pause bot when human agent replies!
        ];

        // Auto-assign to current agent if unassigned
        if (empty($session->assigned_agent_id)) {
            $updateSession['assigned_agent_id'] = $this->currentAgent->agent_id;
        }

        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update($updateSession);

        $newMsg = $this->db->table('tb_chat_messages m')
            ->select('m.*, ag.fullname as agent_fullname, ag.avatar as agent_avatar')
            ->join('tb_chat_agents ag', 'ag.agent_id = m.agent_id', 'left')
            ->where('m.message_id', $msgId)
            ->get()
            ->getRow();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ส่งข้อความสำเร็จ',
            'data'    => $newMsg
        ]);
    }

    public function uploadAttachment()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไฟล์ไม่ถูกต้องหรือมีขนาดใหญ่เกินไป']);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
        $ext = strtolower($file->getClientExtension());

        if (!in_array($ext, $allowedExts)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ประเภทไฟล์ไม่ได้รับอนุญาต (รองรับเฉพาะ รูปภาพ, PDF, Office, Zip)']);
        }

        $uploadPath = FCPATH . 'uploads/chat/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $fullUrl = base_url('uploads/chat/' . $newName);
        $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'file';

        return $this->response->setJSON([
            'status'          => 'success',
            'attachment_url'  => $fullUrl,
            'attachment_type' => $type,
            'file_name'       => $file->getClientName()
        ]);
    }

    public function assignAgent()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $sessionId = (int)$this->request->getPost('session_id');
        $agentId   = (int)$this->request->getPost('agent_id');

        $agentModel = new AgentModel();
        $agent = $agentModel->find($agentId);
        if (!$agent) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบเจ้าหน้าที่']);
        }

        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update([
            'assigned_agent_id' => $agentId,
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'มอบหมายงานให้ ' . $agent->fullname . ' เรียบร้อยแล้ว',
            'agent_name' => $agent->fullname
        ]);
    }

    public function toggleBot($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบเซสชัน']);
        }

        $currentPaused = (int)($session->is_bot_paused ?? 0);
        $newPaused = ($currentPaused === 1) ? 0 : 1;
        $now = date('Y-m-d H:i:s');

        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update([
            'is_bot_paused' => $newPaused,
            'updated_at'    => $now
        ]);

        $botMessage = null;
        if ($newPaused === 0) {
            // AI was enabled: Check if the latest message in this session was from user (unanswered)
            $lastMsg = $this->db->table('tb_chat_messages')
                ->where('session_id', $sessionId)
                ->orderBy('message_id', 'DESC')
                ->get()
                ->getRow();

            if ($lastMsg && $lastMsg->sender_type === 'user') {
                $updatedSession = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
                $botMessage = $this->generateAiReplyForSession($updatedSession, $lastMsg->message);
            }
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'is_bot_paused' => $newPaused,
            'bot_message'   => $botMessage,
            'message'       => ($newPaused === 1) ? 'พักการทำงานของ AI แล้ว (เจ้าหน้าที่ดูแล)' : 'เปิดให้ AI น้องกุหลาบตอบอัตโนมัติแล้ว'
        ]);
    }

    public function triggerAi($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบเซสชัน']);
        }

        $botMessage = $this->generateAiReplyForSession($session);
        if ($botMessage) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'น้องกุหลาบ AI ได้ตอบกลับเรียบร้อยแล้ว',
                'data'    => $botMessage
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'ไม่สามารถสร้างคำตอบจาก AI ได้ กรุณาตรวจสอบการตั้งค่า AI'
        ]);
    }

    public function saveNotes()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $sessionId = (int)$this->request->getPost('session_id');
        $notes     = trim($this->request->getPost('notes') ?? '');
        $userTel   = trim($this->request->getPost('user_tel') ?? '');

        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update([
            'notes'      => $notes,
            'user_tel'   => $userTel,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อย']);
    }

    public function toggleStatus($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบเซสชัน']);
        }

        $newStatus = ($session->status === 'active') ? 'closed' : 'active';
        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update([
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'new_status' => $newStatus,
            'message'    => ($newStatus === 'closed') ? 'ปิดการสนทนาเรียบร้อยแล้ว' : 'เปิดการสนทนาอีกครั้งเรียบร้อยแล้ว'
        ]);
    }

    public function updateOnlineStatus()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $status = $this->request->getPost('status');
        if (!in_array($status, ['online', 'busy', 'offline'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'สถานะไม่ถูกต้อง']);
        }

        $agentModel = new AgentModel();
        $agentModel->update($this->currentAgent->agent_id, [
            'online_status'  => $status,
            'last_active_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'        => 'success',
            'online_status' => $status
        ]);
    }
}