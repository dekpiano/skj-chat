<?php

namespace App\Controllers;

use App\Models\AgentModel;
use App\Models\OauthConfigModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->currentAgent) {
            return redirect()->to(base_url('chat/desk'));
        }

        $oauthModel = new OauthConfigModel();
        $oauthConfig = $oauthModel->find(1);

        $data = [
            'title'       => 'เข้าสู่ระบบ | SKJ Live Chat',
            'oauthConfig' => $oauthConfig,
            'googleClientId' => $oauthConfig ? $oauthConfig->google_client_id : '',
            'allowedDomains' => $oauthConfig ? $oauthConfig->allowed_domains : 'skj.ac.th',
        ];

        return view('auth/login', array_merge($this->data, $data));
    }

    public function verifyGoogle()
    {
        $credential = $this->request->getPost('credential');
        if (empty($credential)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ไม่พบข้อมูล Credential จาก Google'
            ]);
        }

        // Verify ID Token with Google tokeninfo endpoint
        $verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
        $client = \Config\Services::curlrequest();

        try {
            $res = $client->get($verifyUrl, [
                'http_errors' => false,
                'timeout'     => 10
            ]);

            $payload = json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์กับ Google: ' . $e->getMessage()
            ]);
        }

        if (empty($payload) || !isset($payload['email'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ข้อมูล Google Token ไม่ถูกต้องหรือหมดอายุ'
            ]);
        }

        $email    = strtolower(trim($payload['email']));
        $name     = $payload['name'] ?? explode('@', $email)[0];
        $picture  = $payload['picture'] ?? '';
        $googleId = $payload['sub'] ?? '';

        $oauthModel = new OauthConfigModel();
        $oauthConfig = $oauthModel->find(1);

        $allowedDomains = [];
        if ($oauthConfig && !empty($oauthConfig->allowed_domains)) {
            $allowedDomains = array_map('trim', explode(',', strtolower($oauthConfig->allowed_domains)));
        }

        $emailDomain = substr(strrchr($email, "@"), 1);

        $agentModel = new AgentModel();
        $agent = $agentModel->where('email', $email)->first();

        // Count total agents to see if this is the first one (auto-superadmin)
        $totalAgents = $agentModel->countAllResults();

        // Generate secure persistent remember token
        $rawRememberToken = bin2hex(random_bytes(32));
        $hashedRememberToken = hash('sha256', $rawRememberToken);

        if (!$agent) {
            // Check domain permission if not first agent
            if ($totalAgents > 0 && !empty($allowedDomains) && !in_array($emailDomain, $allowedDomains)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => "อีเมล {$email} ไม่อยู่ในโดเมนที่ได้รับอนุญาต (@" . implode(', @', $allowedDomains) . ")"
                ]);
            }

            // Create new agent account
            $adminEmails = ['dekpiano@skj.ac.th', 'admin@skj.ac.th'];
            $role = ($totalAgents === 0 || in_array($email, $adminEmails)) ? 'superadmin' : 'agent';
            $insertData = [
                'google_id'      => $googleId,
                'email'          => $email,
                'fullname'       => $name,
                'avatar'         => $picture,
                'remember_token' => $hashedRememberToken,
                'role'           => $role,
                'status'         => 'active',
                'online_status'  => 'online',
                'last_login_at'  => date('Y-m-d H:i:s'),
                'last_active_at' => date('Y-m-d H:i:s'),
            ];

            $newAgentId = $agentModel->insert($insertData);
            $agent = $agentModel->find($newAgentId);
        } else {
            if ($agent->status !== 'active') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'บัญชีนี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ'
                ]);
            }

            $adminEmails = ['dekpiano@skj.ac.th', 'admin@skj.ac.th'];
            $updateData = [
                'google_id'      => $googleId,
                'fullname'       => $name ?: $agent->fullname,
                'avatar'         => $picture ?: $agent->avatar,
                'remember_token' => $hashedRememberToken,
                'online_status'  => 'online',
                'last_login_at'  => date('Y-m-d H:i:s'),
                'last_active_at' => date('Y-m-d H:i:s'),
            ];
            if (in_array($email, $adminEmails) && $agent->role !== 'superadmin') {
                $updateData['role'] = 'superadmin';
            }

            $agentModel->update($agent->agent_id, $updateData);
            $agent = $agentModel->find($agent->agent_id);
        }

        // Set session
        $this->session->set([
            'chat_agent_id'     => $agent->agent_id,
            'chat_agent_email'  => $agent->email,
            'chat_agent_name'   => $agent->fullname,
            'chat_agent_role'   => $agent->role,
            'chat_agent_avatar' => $agent->avatar,
            'chat_logged_in'    => true
        ]);

        // Set persistent remember cookie (30 days)
        set_cookie([
            'name'     => 'skj_agent_remember',
            'value'    => $rawRememberToken,
            'expire'   => 2592000, // 30 days
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $redirect = $this->session->get('redirect_after_login') ?? base_url('chat/desk');
        $this->session->remove('redirect_after_login');

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => 'เข้าสู่ระบบสำเร็จ',
            'redirect' => $redirect
        ]);
    }

    public function devLogin()
    {
        // Allowed in development environment for initial testing before setting Google Client ID
        if (ENVIRONMENT === 'production') {
            return redirect()->to(base_url('auth/login'));
        }

        $agentModel = new AgentModel();
        $admin = $agentModel->where('role', 'superadmin')->first();

        $rawRememberToken = bin2hex(random_bytes(32));
        $hashedRememberToken = hash('sha256', $rawRememberToken);

        if (!$admin) {
            $adminId = $agentModel->insert([
                'email'          => 'admin@skj.ac.th',
                'fullname'       => 'ผู้ดูแลระบบระบบแชท SKJ',
                'avatar'         => 'https://ui-avatars.com/api/?name=Admin+SKJ&background=e91e63&color=fff',
                'remember_token' => $hashedRememberToken,
                'role'           => 'superadmin',
                'status'         => 'active',
                'online_status'  => 'online',
                'last_login_at'  => date('Y-m-d H:i:s'),
                'last_active_at' => date('Y-m-d H:i:s'),
            ]);
            $admin = $agentModel->find($adminId);
        } else {
            $agentModel->update($admin->agent_id, [
                'remember_token' => $hashedRememberToken,
                'online_status'  => 'online',
                'last_login_at'  => date('Y-m-d H:i:s'),
                'last_active_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->session->set([
            'chat_agent_id'     => $admin->agent_id,
            'chat_agent_email'  => $admin->email,
            'chat_agent_name'   => $admin->fullname,
            'chat_agent_role'   => $admin->role,
            'chat_agent_avatar' => $admin->avatar,
            'chat_logged_in'    => true
        ]);

        set_cookie([
            'name'     => 'skj_agent_remember',
            'value'    => $rawRememberToken,
            'expire'   => 2592000,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return redirect()->to(base_url('chat/desk'))->with('success', 'เข้าสู่ระบบโหมดทดสอบสำเร็จ');
    }

    public function logout()
    {
        if ($this->currentAgent) {
            $agentModel = new AgentModel();
            $agentModel->update($this->currentAgent->agent_id, [
                'online_status'  => 'offline',
                'remember_token' => null
            ]);
        }

        delete_cookie('skj_agent_remember');
        $this->session->destroy();

        return redirect()->to(base_url('auth/login'));
    }
}