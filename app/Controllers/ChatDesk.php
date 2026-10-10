<?php

namespace App\Controllers;

use App\Models\ChatSessionModel;
use App\Models\ChatMessageModel;
use App\Models\AgentModel;
use App\Models\CannedReplyModel;
use App\Models\KnowledgeModel;

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

        $targetSession = $this->request->getGet('session') ?? ($this->request->getGet('session_id') ?? '');
        $targetSessionId = null;
        if (!empty($targetSession)) {
            if (is_numeric($targetSession)) {
                $targetSessionId = (int)$targetSession;
            } else {
                $sRow = $this->db->table('tb_chat_sessions')->where('session_token', $targetSession)->get()->getRow();
                if ($sRow) {
                    $targetSessionId = (int)$sRow->session_id;
                }
            }
        }

        $data = [
            'title'           => 'ศูนย์ควบคุมการสนทนาสด (Live Chat Desk)',
            'activeMenu'      => 'chat_desk',
            'cannedReplies'   => $cannedReplies,
            'agents'          => $agents,
            'aiConfig'        => $aiConfig,
            'activeSession'   => $targetSession,
            'targetSessionId' => $targetSessionId,
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

    private function doExtractKnowledgeFromSession($sessionId)
    {
        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return ['status' => 'error', 'message' => 'ไม่พบข้อมูลการสนทนา'];
        }

        $messages = $this->db->table('tb_chat_messages')
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResult();

        if (empty($messages) || count($messages) < 2) {
            return ['status' => 'error', 'message' => 'บทสนทนามีข้อความน้อยเกินไปสำหรับการสกัดความรู้ (ต้องมีอย่างน้อย 2 ข้อความ)'];
        }

        $dialogue = "";
        foreach ($messages as $m) {
            $sender = ($m->sender_type === 'user') ? 'ผู้ปกครอง/นักเรียน' : (($m->sender_type === 'bot') ? 'น้องกุหลาบ AI' : 'เจ้าหน้าที่/ครู');
            $msg = trim($m->message ?? '');
            if (!empty($msg)) {
                $dialogue .= "[{$sender}]: {$msg}\n";
            }
        }

        if (empty(trim($dialogue))) {
            return ['status' => 'error', 'message' => 'ไม่มีข้อความตัวอักษรในบทสนทนา'];
        }

        $aiConfig = $this->db->table('tb_chat_ai_config')->where('ai_id', 1)->get()->getRow();
        if (!$aiConfig || empty($aiConfig->ai_api_key)) {
            return ['status' => 'error', 'message' => 'ยังไม่ได้ตั้งค่า Google Gemini API Key ในระบบ'];
        }

        $systemPrompt = "คุณคือนักจัดการความรู้อาวุโสของโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ หน้าที่ของคุณคือวิเคราะห์บทสนทนาที่เกิดขึ้นจริงระหว่างผู้ปกครอง/นักเรียน กับครูหรือเจ้าหน้าที่ แล้วสกัด 'สาระสำคัญและองค์ความรู้ที่ถูกต้อง' เพื่อบันทึกลงในคลังความรู้ AI (RAG Knowledge Base)\n\n"
            . "กรุณาตอบเป็นรูปแบบ JSON Object เท่านั้น (ห้ามมี Markdown Backticks หรือข้อความอื่นนอก JSON) โดยมีโครงสร้างดังนี้:\n"
            . "{\n"
            . "  \"title\": \"หัวข้อเรื่องที่กระชับ ตรงประเด็น ชัดเจน (เช่น ขั้นตอนการขอใบรับรองผลการเรียน ปพ.1, กำหนดการมอบตัวนักเรียน ม.1 และ ม.4)\",\n"
            . "  \"category\": \"วิเคราะห์และกำหนดชื่อหมวดหมู่งานที่ตรงกับเนื้อหาโดยอัตโนมัติ (เช่น การรับสมัครนักเรียน, งานวิชาการและตารางสอบ, ทุนการศึกษาและสวัสดิการ, งานทะเบียนและเอกสาร ปพ., กิจกรรมพัฒนาผู้เรียน, การเงินและค่าบำรุงการศึกษา, การแนะแนวและศึกษาต่อ, อาคารสถานที่และหอพัก, ข้อมูลทั่วไปและการติดต่อ หรือสร้างชื่อหมวดหมู่ใหม่ที่กระชับและตรงกับบริบทที่สุด)\",\n"
            . "  \"summary\": \"เนื้อหาความรู้อย่างละเอียด สรุปสาระสำคัญ คำถามและคำตอบที่ถูกต้อง ครบถ้วน สละสลวย อ่านง่าย ไพเราะ พร้อมข้อกำหนด ขั้นตอน หรือเบอร์โทรติดต่อถ้ามี\",\n"
            . "  \"keywords\": \"คำค้นหาหลักที่เกี่ยวข้อง 3-6 คำ คั่นด้วยจุลภาค\"\n"
            . "}";

        $userPrompt = "บทสนทนาที่ต้องการให้สกัดความรู้:\n\n" . $dialogue;

        $primaryModel = $aiConfig->ai_model ?: 'gemini-3.5-flash';
        $modelsToTry = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite'
        ])));

        $client = \Config\Services::curlrequest();
        $extractedData = null;

        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $aiConfig->ai_api_key;

            try {
                $res = $client->post($url, [
                    'json' => [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemPrompt]]
                        ],
                        'contents' => [
                            [
                                'role'  => 'user',
                                'parts' => [['text' => $userPrompt]]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature'       => 0.2,
                            'maxOutputTokens'   => 1500,
                            'responseMimeType'  => 'application/json'
                        ]
                    ],
                    'http_errors' => false,
                    'timeout'     => 20
                ]);

                $result = json_decode($res->getBody(), true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = trim($result['candidates'][0]['content']['parts'][0]['text']);
                    $jsonText = preg_replace('/^```(?:json)?\s*/i', '', $jsonText);
                    $jsonText = preg_replace('/\s*```$/', '', $jsonText);
                    $parsed = json_decode($jsonText, true);
                    if ($parsed && !empty($parsed['title']) && !empty($parsed['summary'])) {
                        $extractedData = $parsed;
                        break;
                    }
                }
            } catch (\Exception $e) {}
        }

        if (!$extractedData) {
            return ['status' => 'error', 'message' => 'ไม่สามารถสกัดความรู้ด้วย AI ได้ กรุณาลองใหม่อีกครั้ง'];
        }

        return [
            'status'  => 'success',
            'data'    => $extractedData,
            'session' => $session
        ];
    }

    public function previewKnowledge($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $res = $this->doExtractKnowledgeFromSession($sessionId);
        if ($res['status'] !== 'success') {
            return $this->response->setJSON($res);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'title'    => trim($res['data']['title']),
            'category' => trim($res['data']['category'] ?? 'ทั่วไป'),
            'summary'  => trim($res['data']['summary']),
            'keywords' => trim($res['data']['keywords'] ?? '')
        ]);
    }

    public function saveExtractedKnowledge()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $sessionId = (int)$this->request->getPost('session_id');
        $title     = trim($this->request->getPost('title') ?? '');
        $category  = trim($this->request->getPost('category') ?? 'ทั่วไป');
        $summary   = trim($this->request->getPost('summary') ?? '');
        $keywords  = trim($this->request->getPost('keywords') ?? '');
        $autoClose = (int)$this->request->getPost('auto_close');

        if (empty($title) || empty($summary)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุหัวข้อและเนื้อหาความรู้']);
        }

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        $sourceUrl = $session ? base_url("chat/desk?session={$session->session_token}") : '';

        $content = "【หมวดหมู่: {$category}】\n{$summary}\n\nคำสำคัญ: {$keywords}\n(สกัดความรู้อัตโนมัติจากบทสนทนาจริง รหัสเซสชัน #{$sessionId} เมื่อ " . date('d/m/Y H:i') . ")";

        $knowledgeModel = new KnowledgeModel();
        $insertId = $knowledgeModel->insert([
            'title'       => "【แชท】" . $title,
            'source_type' => 'chat',
            'source_url'  => $sourceUrl,
            'content'     => $content,
            'char_count'  => mb_strlen($content),
            'status'      => 'on'
        ]);

        if ($autoClose === 1 && $session && $session->status === 'active') {
            $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->update([
                'status'     => 'closed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'status'       => 'success',
            'knowledge_id' => $insertId,
            'title'        => $title,
            'message'      => 'บันทึกเข้าคลังความรู้ AI เรียบร้อยแล้ว' . ($autoClose === 1 ? ' พร้อมปิดการสนทนา' : '')
        ]);
    }

    public function extractKnowledge($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $res = $this->doExtractKnowledgeFromSession($sessionId);
        if ($res['status'] !== 'success') {
            return $this->response->setJSON($res);
        }

        $session = $res['session'];
        $title = trim($res['data']['title']);
        $category = trim($res['data']['category'] ?? 'ทั่วไป');
        $summary = trim($res['data']['summary']);
        $keywords = trim($res['data']['keywords'] ?? '');

        $content = "【หมวดหมู่: {$category}】\n{$summary}\n\nคำสำคัญ: {$keywords}\n(สกัดความรู้อัตโนมัติจากบทสนทนาจริง รหัสเซสชัน #{$sessionId} เมื่อ " . date('d/m/Y H:i') . ")";

        $knowledgeModel = new KnowledgeModel();
        $insertId = $knowledgeModel->insert([
            'title'       => "【แชท】" . $title,
            'source_type' => 'chat',
            'source_url'  => base_url("chat/desk?session={$session->session_token}"),
            'content'     => $content,
            'char_count'  => mb_strlen($content),
            'status'      => 'on'
        ]);

        return $this->response->setJSON([
            'status'       => 'success',
            'knowledge_id' => $insertId,
            'title'        => $title,
            'category'     => $category,
            'summary'      => $summary,
            'message'      => 'บันทึกเข้าคลังความรู้ AI เรียบร้อยแล้ว'
        ]);
    }

    public function deleteSession($sessionId)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลการสนทนา']);
        }

        // Delete associated files
        $messagesWithFiles = $this->db->table('tb_chat_messages')
            ->where('session_id', $sessionId)
            ->where('attachment_url IS NOT NULL')
            ->get()
            ->getResult();

        foreach ($messagesWithFiles as $m) {
            if (!empty($m->attachment_url)) {
                $filename = basename($m->attachment_url);
                $filePath = FCPATH . 'uploads/chat/' . $filename;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        // Delete messages & session
        $this->db->table('tb_chat_messages')->where('session_id', $sessionId)->delete();
        $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ลบประวัติการสนทนาและไฟล์แนบทั้งหมดเรียบร้อยแล้ว'
        ]);
    }

    public function cleanupHistory()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $days = (int)($this->request->getPost('days') ?? 90);
        $type = $this->request->getPost('type') ?? 'all'; // all, attachments_only

        if ($days < 7) $days = 7;
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $closedSessions = $this->db->table('tb_chat_sessions')
            ->where('status', 'closed')
            ->where('updated_at <', $cutoffDate)
            ->get()
            ->getResult();

        $deletedSessionsCount = 0;
        $deletedFilesCount = 0;

        foreach ($closedSessions as $s) {
            $files = $this->db->table('tb_chat_messages')
                ->where('session_id', $s->session_id)
                ->where('attachment_url IS NOT NULL')
                ->get()
                ->getResult();

            foreach ($files as $f) {
                if (!empty($f->attachment_url)) {
                    $filename = basename($f->attachment_url);
                    $filePath = FCPATH . 'uploads/chat/' . $filename;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                        $deletedFilesCount++;
                    }
                }
            }

            if ($type === 'all') {
                $this->db->table('tb_chat_messages')->where('session_id', $s->session_id)->delete();
                $this->db->table('tb_chat_sessions')->where('session_id', $s->session_id)->delete();
                $deletedSessionsCount++;
            }
        }

        return $this->response->setJSON([
            'status'           => 'success',
            'deleted_sessions' => $deletedSessionsCount,
            'deleted_files'    => $deletedFilesCount,
            'message'          => "ล้างข้อมูลที่เก่ากว่า {$days} วันเรียบร้อยแล้ว (ลบ {$deletedSessionsCount} การสนทนา, {$deletedFilesCount} ไฟล์)"
        ]);
    }
}