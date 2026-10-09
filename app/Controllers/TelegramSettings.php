<?php

namespace App\Controllers;

use App\Models\TelegramConfigModel;

class TelegramSettings extends BaseController
{
    public function index()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $tgModel = new TelegramConfigModel();
        $tgConfig = $tgModel->find(1);

        $data = [
            'title'      => 'ตั้งค่าการแจ้งเตือน Telegram',
            'activeMenu' => 'telegram_settings',
            'tgConfig'   => $tgConfig,
        ];

        return view('settings/telegram', array_merge($this->data, $data));
    }

    public function save()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $token  = trim($this->request->getPost('telegram_bot_token') ?? '');
        $chatId = trim($this->request->getPost('telegram_chat_id') ?? '');
        $title  = trim($this->request->getPost('telegram_chat_title') ?? 'SKJ Live Chat');
        $status = $this->request->getPost('telegram_status') === 'on' ? 'on' : 'off';

        $tgModel = new TelegramConfigModel();
        $tgModel->update(1, [
            'telegram_bot_token'  => $token,
            'telegram_chat_id'    => $chatId,
            'telegram_chat_title' => $title,
            'telegram_status'     => $status,
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'บันทึกการตั้งค่า Telegram สำเร็จ'
        ]);
    }

    public function testNotification()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $tgModel = new TelegramConfigModel();
        $tgConfig = $tgModel->find(1);

        if (!$tgConfig || empty($tgConfig->telegram_bot_token) || empty($tgConfig->telegram_chat_id)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'กรุณาระบุ Telegram Bot Token และ Chat ID ก่อนทดสอบ'
            ]);
        }

        $botToken = $tgConfig->telegram_bot_token;
        $chatId   = $tgConfig->telegram_chat_id;
        $timeStr  = date('d/m/Y H:i:s');

        $text = "🔔 *ทดสอบการแจ้งเตือน SKJ Live Chat*\n"
              . "📅 เวลา: {$timeStr}\n"
              . "👤 ผู้ทดสอบ: {$this->currentAgent->fullname}\n"
              . "✅ ระบบเชื่อมต่อ Telegram Bot สำเร็จพร้อมใช้งาน!";

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $client = \Config\Services::curlrequest();

        try {
            $res = $client->post($url, [
                'form_params' => [
                    'chat_id'    => $chatId,
                    'text'       => $text,
                    'parse_mode' => 'Markdown'
                ],
                'http_errors' => false,
                'timeout'     => 10
            ]);

            $resData = json_decode($res->getBody(), true);
            if (!empty($resData['ok'])) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'ส่งข้อความทดสอบไปยัง Telegram สำเร็จ!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Telegram แจ้งเตือนไม่สำเร็จ: ' . ($resData['description'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' . $e->getMessage()
            ]);
        }
    }
}