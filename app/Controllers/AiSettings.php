<?php

namespace App\Controllers;

use App\Models\AiConfigModel;

class AiSettings extends BaseController
{
    public function index()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $aiModel = new AiConfigModel();
        $aiConfig = $aiModel->find(1);

        $data = [
            'title'      => 'ตั้งค่า Google Gemini AI Assistant',
            'activeMenu' => 'ai_settings',
            'aiConfig'   => $aiConfig,
        ];

        return view('settings/ai', array_merge($this->data, $data));
    }

    public function save()
    {
        if ($redir = $this->requireRole(['superadmin', 'admin'])) return $redir;

        $apiKey       = trim($this->request->getPost('ai_api_key') ?? '');
        $model        = trim($this->request->getPost('ai_model') ?? 'gemini-3.8-flash');
        $systemPrompt = trim($this->request->getPost('ai_system_prompt') ?? '');
        $status       = $this->request->getPost('ai_status') === 'on' ? 'on' : 'off';
        $temperature  = (float)$this->request->getPost('ai_temperature');
        $maxTokens    = (int)$this->request->getPost('ai_max_tokens');

        $aiModel = new AiConfigModel();
        $aiModel->update(1, [
            'ai_api_key'       => $apiKey,
            'ai_model'         => $model,
            'ai_system_prompt' => $systemPrompt,
            'ai_status'        => $status,
            'ai_temperature'   => $temperature ?: 0.7,
            'ai_max_tokens'    => $maxTokens ?: 500,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'บันทึกการตั้งค่า AI สำเร็จ'
        ]);
    }

    public function testAi()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $prompt = trim($this->request->getPost('prompt') ?? 'สวัสดีครับ โรงเรียนเปิดรับสมัครเมื่อไหร่ครับ');

        $aiModel = new AiConfigModel();
        $aiConfig = $aiModel->find(1);

        if (!$aiConfig || empty($aiConfig->ai_api_key)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ยังไม่ได้ระบุ Google Gemini API Key ในระบบ'
            ]);
        }

        // Test API call to Gemini
        $primaryModel = $aiConfig->ai_model ?: 'gemini-3.5-flash';
        $client = \Config\Services::curlrequest();

        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => (float)$aiConfig->ai_temperature,
                'maxOutputTokens' => (int)$aiConfig->ai_max_tokens,
            ]
        ];

        if (!empty($aiConfig->ai_system_prompt)) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $aiConfig->ai_system_prompt]
                ]
            ];
        }

        $modelsToTry = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite'
        ])));

        $lastError = 'ไม่สามารถสร้างคำตอบจาก Gemini ได้';
        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $aiConfig->ai_api_key;
            try {
                $res = $client->post($url, [
                    'json'        => $payload,
                    'http_errors' => false,
                    'timeout'     => 15
                ]);

                $result = json_decode($res->getBody(), true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
                    return $this->response->setJSON([
                        'status' => 'success',
                        'reply'  => $reply,
                    ]);
                } else if (isset($result['error']['message'])) {
                    $lastError = $result['error']['message'];
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
            }
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => $lastError
        ]);
    }
}