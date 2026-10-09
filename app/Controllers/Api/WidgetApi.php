<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class WidgetApi extends BaseController
{
    protected function setCorsHeaders()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Session-Token');
        if ($this->request->getMethod() === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }

    public function initSession()
    {
        $this->setCorsHeaders();

        $token    = trim($this->request->getPost('session_token') ?? '');
        $userName = trim($this->request->getPost('user_name') ?? 'ผู้เยี่ยมชม');
        $userTel  = trim($this->request->getPost('user_tel') ?? '');
        $userIp   = $this->request->getIPAddress();
        $ua       = $this->request->getUserAgent()->getAgentString();

        $now = date('Y-m-d H:i:s');

        if (!empty($token)) {
            $session = $this->db->table('tb_chat_sessions')->where('session_token', $token)->get()->getRow();
            if ($session) {
                // Resume session
                $this->db->table('tb_chat_sessions')->where('session_id', $session->session_id)->update([
                    'updated_at' => $now,
                    'user_ip'    => $userIp,
                    'user_agent' => $ua,
                ]);

                $messages = $this->db->table('tb_chat_messages')
                    ->where('session_id', $session->session_id)
                    ->orderBy('created_at', 'ASC')
                    ->get()
                    ->getResult();

                return $this->response->setJSON([
                    'status'   => 'success',
                    'session'  => $session,
                    'messages' => $messages,
                    'is_new'   => false
                ]);
            }
        }

        // Create new session
        $newToken = bin2hex(random_bytes(16));
        $this->db->table('tb_chat_sessions')->insert([
            'session_token'      => $newToken,
            'user_name'          => $userName,
            'user_tel'           => $userTel,
            'user_ip'            => $userIp,
            'user_agent'         => $ua,
            'status'             => 'active',
            'unread_user_count'  => 0,
            'unread_admin_count' => 0,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);
        $sessionId = $this->db->insertID();

        // System greeting
        $greeting = [
            'session_id'      => $sessionId,
            'sender_type'     => 'system',
            'sender_name'     => 'ระบบ SKJ Live Chat',
            'message'         => "สวัสดีครับ/ค่ะ ยินดีต้อนรับสู่ระบบสนทนาสด โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์\nสามารถพิมพ์สอบถามข้อมูล หรือฝากข้อความไว้ได้เลยครับ เจ้าหน้าที่และ AI Assistant พร้อมให้บริการครับ 🌸",
            'is_bot'          => 1,
            'is_read'         => 1,
            'created_at'      => $now
        ];
        $this->db->table('tb_chat_messages')->insert($greeting);

        $session = $this->db->table('tb_chat_sessions')->where('session_id', $sessionId)->get()->getRow();
        $messages = $this->db->table('tb_chat_messages')->where('session_id', $sessionId)->orderBy('created_at', 'ASC')->get()->getResult();

        // Alert Telegram on new session initiation
        $this->sendTelegramAlert($session, '✨ เริ่มต้นการติดต่อใหม่ผ่านวิดเจ็ตหน้าเว็บ', null);

        return $this->response->setJSON([
            'status'   => 'success',
            'session'  => $session,
            'messages' => $messages,
            'is_new'   => true
        ]);
    }

    public function pollMessages()
    {
        $this->setCorsHeaders();

        $token   = $this->request->getGet('token') ?? '';
        $afterId = (int)($this->request->getGet('after_id') ?? 0);

        if (empty($token)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Token required']);
        }

        $session = $this->db->table('tb_chat_sessions')->where('session_token', $token)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Session not found']);
        }

        // Mark user unread to 0
        if ($session->unread_user_count > 0) {
            $this->db->table('tb_chat_sessions')->where('session_id', $session->session_id)->update(['unread_user_count' => 0]);
        }

        $query = $this->db->table('tb_chat_messages')
            ->where('session_id', $session->session_id);

        if ($afterId > 0) {
            $query->where('message_id >', $afterId);
        }

        $messages = $query->orderBy('created_at', 'ASC')->get()->getResult();

        return $this->response->setJSON([
            'status'         => 'success',
            'session'        => $session,
            'messages'       => $messages,
            'is_bot_paused'  => (int)$session->is_bot_paused,
            'session_status' => $session->status,
        ]);
    }

    public function sendMessage()
    {
        $this->setCorsHeaders();

        $token          = $this->request->getPost('token') ?? '';
        $message        = trim($this->request->getPost('message') ?? '');
        $attachmentUrl  = $this->request->getPost('attachment_url') ?? null;
        $attachmentType = $this->request->getPost('attachment_type') ?? null;

        if (empty($token)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Token required']);
        }

        $session = $this->db->table('tb_chat_sessions')->where('session_token', $token)->get()->getRow();
        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Session not found']);
        }

        $now = date('Y-m-d H:i:s');

        // Insert user message
        $this->db->table('tb_chat_messages')->insert([
            'session_id'      => $session->session_id,
            'sender_type'     => 'user',
            'sender_name'     => $session->user_name,
            'message'         => $message,
            'attachment_url'  => $attachmentUrl,
            'attachment_type' => $attachmentType,
            'is_bot'          => 0,
            'is_read'         => 0,
            'created_at'      => $now
        ]);
        $msgId = $this->db->insertID();

        // Update session
        $this->db->table('tb_chat_sessions')->where('session_id', $session->session_id)->update([
            'updated_at'          => $now,
            'unread_admin_count'  => $session->unread_admin_count + 1,
        ]);

        // Send Telegram alert
        $this->sendTelegramAlert($session, $message, $attachmentUrl);

        // Check if AI Bot should answer
        $botReply = null;
        if (!$session->is_bot_paused) {
            $botReply = $this->tryAiReply($session, $message);
        }

        $newMsg = $this->db->table('tb_chat_messages')->where('message_id', $msgId)->get()->getRow();

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => $newMsg,
            'bot_reply' => is_array($botReply) ? ($botReply['message'] ?? null) : $botReply,
            'bot_msg'   => $botReply
        ]);
    }

    public function uploadAttachment()
    {
        $this->setCorsHeaders();

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไฟล์ไม่ถูกต้อง']);
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'zip'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExts)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'นามสกุลไฟล์ไม่ถูกต้อง']);
        }

        $uploadPath = FCPATH . 'uploads/chat/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

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

    protected function sendTelegramAlert($session, $message, $attachmentUrl)
    {
        try {
            $tg = $this->db->table('tb_telegram_config')->where('telegram_id', 1)->get()->getRow();
            if (!$tg || $tg->telegram_status !== 'on' || empty($tg->telegram_bot_token) || empty($tg->telegram_chat_id)) {
                return;
            }

            $deskUrl = base_url('chat/desk?session=' . $session->session_id);
            $msgPreview = $message ?: ($attachmentUrl ? '[แนบไฟล์/รูปภาพ]' : 'เริ่มต้นการสนทนาใหม่');

            $text = "🔔 *มีข้อความใหม่จากผู้ใช้ (SKJ Live Chat)*\n\n"
                  . "👤 *ชื่อผู้ติดต่อ:* " . ($session->user_name ?: 'ผู้ใช้งานทั่วไป') . "\n"
                  . "📞 *เบอร์โทร:* " . ($session->user_tel ?: 'ไม่ได้ระบุ') . "\n"
                  . "💬 *ข้อความ:* " . mb_substr($msgPreview, 0, 300) . "\n"
                  . "⏰ *เวลา:* " . date('d/m/Y H:i:s') . "\n\n"
                  . "👉 [คลิกเพื่อเปิดหน้าจอ Live Chat Desk ตอบกลับ]({$deskUrl})";

            $inlineKeyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '💬 เปิดตอบกลับใน Live Chat Desk',
                            'url'  => $deskUrl
                        ]
                    ]
                ]
            ];

            $client = \Config\Services::curlrequest();
            $client->post("https://api.telegram.org/bot{$tg->telegram_bot_token}/sendMessage", [
                'form_params' => [
                    'chat_id'      => $tg->telegram_chat_id,
                    'text'         => $text,
                    'parse_mode'   => 'Markdown',
                    'reply_markup' => json_encode($inlineKeyboard)
                ],
                'http_errors' => false,
                'timeout'     => 5
            ]);
        } catch (\Exception $e) {
            // Ignore error so client chat flow doesn't fail
        }
    }

    protected function tryAiReply($session, $userMsg)
    {
        if (empty($userMsg)) return null;

        $aiConfig = $this->db->table('tb_chat_ai_config')->where('ai_id', 1)->get()->getRow();
        if (!$aiConfig || $aiConfig->ai_status !== 'on' || empty($aiConfig->ai_api_key)) {
            return null;
        }

        // Load Knowledge Base
        $knowledgeItems = $this->db->table('tb_chat_ai_knowledge')->where('status', 'on')->get()->getResult();
        $knowledgeText = '';
        foreach ($knowledgeItems as $k) {
            $knowledgeText .= "--- ข้อมูล: {$k->title} ---\n" . mb_substr($k->content, 0, 2000) . "\n\n";
        }

        $formatGuide = "\n\nกฎการตอบคำถาม:\n"
                     . "- เขียนข้อความให้อ่านง่าย กระชับ เป็นระเบียบ เว้นบรรทัดแบบธรรมชาติพอดีๆ (ไม่เว้นบรรทัดว่างเปล่าระหว่างรายการ ให้แสดงรายการหรือรายชื่อเรียงติดกันอย่างเป็นระเบียบ)\n"
                     . "- หากมีลิงก์ ให้แสดงเป็นบรรทัดเดี่ยวชัดเจน เช่น '👉 [คลิกที่นี่เพื่อเข้าสู่ระบบรับสมัคร](https://admission.skj.ac.th)'\n"
                     . "- หากมีเบอร์โทรศัพท์ ให้ระบุเบอร์หลักเพียง 1-2 เบอร์อย่างกระชับ เช่น '📞 โทร. 056-009-667 หรือ 056-200-765' ระบบจะแปลงเป็นปุ่มโทรด่วนให้อัตโนมัติ\n"
                     . "- ตอบด้วยความเป็นมิตร อบอุ่น สุภาพ ไพเราะ ใช้คำลงท้าย ค่ะ/นะคะ ในนาม 'น้องกุหลาบ (SKJ AI Assistant)' 🌸\n"
                     . "- หากคำถามเป็นเรื่องส่วนบุคคลที่ไม่มีในระบบ หรือต้องการติดต่อเจ้าหน้าที่ ให้แนะนำโทร 056-009-667 หรือพิมพ์ฝากชื่อ-เบอร์โทรไว้ในแชทเพื่อให้คุณครูติดต่อกลับนะคะ";

        $systemInstruction = ($aiConfig->ai_system_prompt ?? '') . $formatGuide . "\n\n"
                           . "คลังข้อมูลอ้างอิงของโรงเรียน (Knowledge Base):\n" . $knowledgeText;

        $primaryModel = $aiConfig->ai_model ?: 'gemini-3.5-flash';
        $client = \Config\Services::curlrequest();

        // Fetch last 6 messages for context
        $history = $this->db->table('tb_chat_messages')
            ->where('session_id', $session->session_id)
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResult();
        $history = array_reverse($history);

        $rawContents = [];
        foreach ($history as $h) {
            $msgText = trim($h->message ?? '');
            if ($msgText === '') continue;
            $rawContents[] = [
                'role'  => ($h->sender_type === 'user') ? 'user' : 'model',
                'parts' => [['text' => $msgText]]
            ];
        }

        // Normalize consecutive turns with the same role for Gemini API compliance
        $contents = [];
        foreach ($rawContents as $item) {
            $lastIdx = count($contents) - 1;
            if ($lastIdx >= 0 && $contents[$lastIdx]['role'] === $item['role']) {
                $contents[$lastIdx]['parts'][] = $item['parts'][0];
            } else {
                $contents[] = $item;
            }
        }

        if (empty($contents)) {
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userMsg]]
            ];
        }

        // Try primary model first, with fallbacks to fast active Gemini models
        $modelsToTry = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite'
        ])));

        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $aiConfig->ai_api_key;

            try {
                $res = $client->post($url, [
                    'json' => [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemInstruction]]
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature'     => (float)$aiConfig->ai_temperature,
                            'maxOutputTokens' => (int)$aiConfig->ai_max_tokens,
                        ]
                    ],
                    'http_errors' => false,
                    'timeout'     => 15
                ]);

                $result = json_decode($res->getBody(), true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $botText = trim($result['candidates'][0]['content']['parts'][0]['text']);
                    $now = date('Y-m-d H:i:s');

                    $this->db->table('tb_chat_messages')->insert([
                        'session_id'  => $session->session_id,
                        'sender_type' => 'system',
                        'sender_name' => 'น้องกุหลาบ (SKJ AI Assistant)',
                        'message'     => $botText,
                        'is_bot'      => 1,
                        'is_read'     => 1,
                        'created_at'  => $now
                    ]);
                    $botMsgId = $this->db->insertID();

                    $this->db->table('tb_chat_sessions')->where('session_id', $session->session_id)->update([
                        'updated_at'        => $now,
                        'unread_user_count' => $session->unread_user_count + 1
                    ]);

                    return [
                        'message_id'  => $botMsgId,
                        'session_id'  => $session->session_id,
                        'sender_type' => 'system',
                        'sender_name' => 'น้องกุหลาบ (SKJ AI Assistant)',
                        'message'     => $botText,
                        'is_bot'      => 1,
                        'is_read'     => 1,
                        'created_at'  => $now
                    ];
                } else if (isset($result['error'])) {
                    log_message('error', "Gemini AI Error ({$modelName}): " . json_encode($result['error'], JSON_UNESCAPED_UNICODE));
                }
            } catch (\Exception $e) {
                log_message('error', "Gemini AI Exception ({$modelName}): " . $e->getMessage());
            }
        }

        return null;
    }
}