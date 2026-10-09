<?php

namespace App\Controllers;

use App\Models\CannedReplyModel;

class CannedReplies extends BaseController
{
    public function index()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $cannedModel = new CannedReplyModel();
        $replies = $cannedModel->orderBy('shortcut', 'ASC')->findAll();

        $data = [
            'title'      => 'ข้อความตอบกลับด่วน (Canned Replies)',
            'activeMenu' => 'canned_replies',
            'replies'    => $replies
        ];

        return view('canned/index', array_merge($this->data, $data));
    }

    public function saveReply()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $replyId  = (int)$this->request->getPost('reply_id');
        $shortcut = trim($this->request->getPost('shortcut') ?? '');
        $title    = trim($this->request->getPost('title') ?? '');
        $message  = trim($this->request->getPost('message') ?? '');

        if (empty($shortcut) || empty($title) || empty($message)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        if ($shortcut[0] !== '/') {
            $shortcut = '/' . $shortcut;
        }

        $cannedModel = new CannedReplyModel();

        if ($replyId > 0) {
            $cannedModel->update($replyId, [
                'shortcut'   => $shortcut,
                'title'      => $title,
                'message'    => $message,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $msg = 'อัปเดตข้อความด่วนสำเร็จ';
        } else {
            $cannedModel->insert([
                'shortcut'   => $shortcut,
                'title'      => $title,
                'message'    => $message,
                'created_by' => $this->currentAgent->agent_id,
            ]);
            $msg = 'เพิ่มข้อความด่วนใหม่สำเร็จ';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function deleteReply($id)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $cannedModel = new CannedReplyModel();
        $cannedModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อความด่วนสำเร็จ']);
    }
}