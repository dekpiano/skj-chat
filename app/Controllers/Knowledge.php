<?php

namespace App\Controllers;

use App\Models\KnowledgeModel;

class Knowledge extends BaseController
{
    public function index()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $knowledgeModel = new KnowledgeModel();
        $items = $knowledgeModel->orderBy('created_at', 'DESC')->findAll();

        $stats = [
            'total'    => count($items),
            'active'   => count(array_filter($items, fn($i) => $i->status === 'on')),
            'urlCount' => count(array_filter($items, fn($i) => $i->source_type === 'url')),
            'docCount' => count(array_filter($items, fn($i) => in_array($i->source_type, ['file', 'pdf', 'doc']))),
        ];

        $data = [
            'title'      => 'จัดการคลังความรู้ AI (Knowledge Base)',
            'activeMenu' => 'knowledge',
            'items'      => $items,
            'stats'      => $stats
        ];

        return view('knowledge/index', array_merge($this->data, $data));
    }

    public function saveUrl()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $url = trim($this->request->getPost('url') ?? '');
        $title = trim($this->request->getPost('title') ?? '');

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'URL ไม่ถูกต้อง']);
        }

        // Fetch URL content
        try {
            $client = \Config\Services::curlrequest();
            $res = $client->get($url, [
                'headers' => ['User-Agent' => 'SKJ-Chatbot-Crawler/1.0'],
                'timeout' => 15,
                'http_errors' => false
            ]);

            $html = $res->getBody();
            // Clean text
            $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
            $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $text);
            $text = strip_tags($text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            if (empty($title)) {
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                    $title = trim($matches[1]);
                } else {
                    $title = parse_url($url, PHP_URL_HOST);
                }
            }

            $knowledgeModel = new KnowledgeModel();
            $knowledgeModel->insert([
                'title'       => $title ?: $url,
                'source_type' => 'url',
                'source_url'  => $url,
                'content'     => mb_substr($text, 0, 15000),
                'char_count'  => mb_strlen($text),
                'status'      => 'on',
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'เพิ่มข้อมูลจากเว็บไซต์เข้าคลังความรู้สำเร็จ (' . mb_strlen($text) . ' ตัวอักษร)'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ไม่สามารถดึงข้อมูลจาก URL ได้: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadFile()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $file = $this->request->getFile('doc_file');
        $title = trim($this->request->getPost('title') ?? '');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไฟล์ไม่ถูกต้อง']);
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['txt', 'pdf', 'doc', 'docx', 'csv', 'json'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'รองรับเฉพาะไฟล์ .txt, .pdf, .docx, .csv']);
        }

        $uploadDir = FCPATH . 'uploads/knowledge/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);
        $fullPath = $uploadDir . $newName;

        $content = '';
        if ($ext === 'txt' || $ext === 'csv' || $ext === 'json') {
            $content = file_get_contents($fullPath);
        } else {
            // Basic text placeholder for binary docs or plain read
            $content = "ไฟล์เอกสารแนบ: " . $file->getClientName() . "\n" . @file_get_contents($fullPath);
        }

        $knowledgeModel = new KnowledgeModel();
        $knowledgeModel->insert([
            'title'       => $title ?: $file->getClientName(),
            'source_type' => in_array($ext, ['pdf', 'doc', 'docx']) ? $ext : 'file',
            'file_path'   => base_url('uploads/knowledge/' . $newName),
            'file_name'   => $file->getClientName(),
            'file_type'   => $ext,
            'file_size'   => $file->getSize(),
            'content'     => mb_substr($content, 0, 15000),
            'char_count'  => mb_strlen($content),
            'status'      => 'on',
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'อัปโหลดเอกสารเข้าคลังความรู้สำเร็จ'
        ]);
    }

    public function saveText()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $title   = trim($this->request->getPost('title') ?? '');
        $content = trim($this->request->getPost('content') ?? '');

        if (empty($title) || empty($content)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกหัวข้อและเนื้อหา']);
        }

        $knowledgeModel = new KnowledgeModel();
        $knowledgeModel->insert([
            'title'       => $title,
            'source_type' => 'text',
            'content'     => $content,
            'char_count'  => mb_strlen($content),
            'status'      => 'on',
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อย']);
    }

    public function toggle($id)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $knowledgeModel = new KnowledgeModel();
        $item = $knowledgeModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล']);
        }

        $newStatus = ($item->status === 'on') ? 'off' : 'on';
        $knowledgeModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['status' => 'success', 'new_status' => $newStatus]);
    }

    public function delete($id)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $knowledgeModel = new KnowledgeModel();
        $knowledgeModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ']);
    }
}