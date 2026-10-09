<?php

namespace App\Controllers;

use App\Models\AgentModel;

class Agents extends BaseController
{
    public function index()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $agentModel = new AgentModel();
        $agents = $agentModel->orderBy('created_at', 'ASC')->findAll();

        $data = [
            'title'      => 'จัดการเจ้าหน้าที่ & สิทธิ์ (Agent Management)',
            'activeMenu' => 'agents',
            'agents'     => $agents
        ];

        return view('agents/index', array_merge($this->data, $data));
    }

    public function saveAgent()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $agentId  = (int)$this->request->getPost('agent_id');
        $email    = strtolower(trim($this->request->getPost('email') ?? ''));
        $fullname = trim($this->request->getPost('fullname') ?? '');
        $role     = $this->request->getPost('role') ?? 'agent';
        $status   = $this->request->getPost('status') ?? 'active';

        if (empty($email) || empty($fullname)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกอีเมลและชื่อ-นามสกุล']);
        }

        $agentModel = new AgentModel();

        if ($agentId > 0) {
            // Edit
            $agentModel->update($agentId, [
                'email'      => $email,
                'fullname'   => $fullname,
                'role'       => $role,
                'status'     => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $msg = 'อัปเดตข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว';
        } else {
            // Create
            if ($agentModel->where('email', $email)->first()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'อีเมลนี้มีอยู่ในระบบแล้ว']);
            }

            $agentModel->insert([
                'email'         => $email,
                'fullname'      => $fullname,
                'avatar'        => "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=random",
                'role'          => $role,
                'status'        => $status,
                'online_status' => 'offline',
            ]);
            $msg = 'เพิ่มเจ้าหน้าที่ใหม่เรียบร้อยแล้ว เจ้าหน้าที่สามารถ Login ด้วย Google ได้ทันที';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function deleteAgent($id)
    {
        if ($redir = $this->requireRole(['superadmin'])) return $redir;

        if ($id == $this->currentAgent->agent_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
        }

        $agentModel = new AgentModel();
        $agentModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบเจ้าหน้าที่สำเร็จ']);
    }
}