<?php

namespace App\Controllers;

use App\Models\OauthConfigModel;

class Settings extends BaseController
{
    public function index()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $oauthModel = new OauthConfigModel();
        $oauthConfig = $oauthModel->find(1);

        $data = [
            'title'       => 'ตั้งค่าระบบ & Google OAuth',
            'activeMenu'  => 'settings',
            'oauthConfig' => $oauthConfig,
        ];

        return view('settings/oauth', array_merge($this->data, $data));
    }

    public function saveOauth()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $clientId       = trim($this->request->getPost('google_client_id') ?? '');
        $clientSecret   = trim($this->request->getPost('google_client_secret') ?? '');
        $allowedDomains = trim($this->request->getPost('allowed_domains') ?? 'skj.ac.th');
        $autoRegister   = $this->request->getPost('auto_register_domain') ? 1 : 0;

        $oauthModel = new OauthConfigModel();
        $oauthModel->update(1, [
            'google_client_id'     => $clientId,
            'google_client_secret' => $clientSecret,
            'allowed_domains'      => $allowedDomains,
            'auto_register_domain' => $autoRegister,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'บันทึกการตั้งค่า Google OAuth เรียบร้อยแล้ว'
        ]);
    }
}