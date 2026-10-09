<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\AgentModel;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'text', 'cookie'];
    protected $session;
    protected $db;
    protected $currentAgent = null;
    protected $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();

        $this->loadCurrentAgent();

        $this->data = [
            'appName'      => 'SKJ Live Chat Console',
            'schoolName'   => 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์',
            'currentAgent' => $this->currentAgent,
            'activeMenu'   => '',
        ];
    }

    protected function loadCurrentAgent()
    {
        $agentModel = new AgentModel();
        $agentId = $this->session->get('chat_agent_id');

        if ($agentId) {
            $this->currentAgent = $agentModel->find($agentId);
        }

        // Auto-restore session from persistent remember cookie if session is expired/missing
        if (!$this->currentAgent) {
            $rememberToken = $this->request->getCookie('skj_agent_remember');
            if ($rememberToken) {
                $hashedToken = hash('sha256', $rememberToken);
                $agent = $agentModel->where('remember_token', $hashedToken)
                                   ->where('status', 'active')
                                   ->first();

                if ($agent) {
                    $this->currentAgent = $agent;
                    // Restore session data
                    $this->session->set([
                        'chat_agent_id'     => $agent->agent_id,
                        'chat_agent_email'  => $agent->email,
                        'chat_agent_name'   => $agent->fullname,
                        'chat_agent_role'   => $agent->role,
                        'chat_agent_avatar' => $agent->avatar,
                        'chat_logged_in'    => true
                    ]);
                } else {
                    // Invalid/revoked token, clear cookie
                    delete_cookie('skj_agent_remember');
                }
            }
        }

        if ($this->currentAgent) {
            // update last_active_at periodically
            $agentModel->update($this->currentAgent->agent_id, [
                'last_active_at' => date('Y-m-d H:i:s'),
                'online_status'  => $this->currentAgent->online_status === 'offline' ? 'online' : $this->currentAgent->online_status
            ]);
        }
    }

    protected function checkAuth()
    {
        if (!$this->currentAgent) {
            $redirectUrl = current_url(true)->__toString();
            $this->session->set('redirect_after_login', $redirectUrl);
            return redirect()->to(base_url('auth/login'));
        }
        return null;
    }

    protected function requireRole(array $allowedRoles)
    {
        if ($redir = $this->checkAuth()) {
            return $redir;
        }

        if (!in_array($this->currentAgent->role, $allowedRoles)) {
            return redirect()->to(base_url('chat/desk'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return null;
    }

    public function generateAiReplyForSession($session, $userMsg = null)
    {
        if (is_numeric($session)) {
            $session = $this->db->table('tb_chat_sessions')->where('session_id', $session)->get()->getRow();
        }
        if (!$session) return null;

        $aiConfig = $this->db->table('tb_chat_ai_config')->where('ai_id', 1)->get()->getRow();
        if (!$aiConfig || $aiConfig->ai_status !== 'on' || empty($aiConfig->ai_api_key)) {
            return null;
        }

        // If no userMsg provided, get the last message in this session
        if (empty($userMsg)) {
            $lastUserMsg = $this->db->table('tb_chat_messages')
                ->where('session_id', $session->session_id)
                ->where('sender_type', 'user')
                ->orderBy('message_id', 'DESC')
                ->get()
                ->getRow();
            $userMsg = $lastUserMsg->message ?? '';
        }

        if (empty($userMsg)) return null;

        // Load Knowledge Base
        $knowledgeItems = $this->db->table('tb_chat_ai_knowledge')->where('status', 'on')->get()->getResult();
        $knowledgeText = '';
        foreach ($knowledgeItems as $k) {
            $knowledgeText .= "--- ข้อมูล: {$k->title} ---\n" . mb_substr($k->content, 0, 2000) . "\n\n";
        }

        $formatGuide = "\n\nกฎการเรียบเรียงและตอบคำถาม (สำคัญมาก):\n"
                     . "- เรียบเรียงประโยคและข้อความให้อ่านง่าย ลื่นไหล สละสลวย เหมือนอ่านบทความหรือหนังสือ\n"
                     . "- ไม่เว้นบรรทัดพร่ำเพรื่อ ไม่เคาะบรรทัดว่างซ้ำซ้อนหลายบรรทัด ให้ข้อความร้อยเรียงกระชับและต่อเนื่องเป็นธรรมชาติ\n"
                     . "- หากมีรายการข้อมูล ให้ใช้เครื่องหมายจุดนำหัวข้อ (•) เรียงต่อกันอย่างกระชับ ไม่ต้องเว้นบรรทัดว่างคั่นระหว่างข้อ\n"
                     . "- หากมีลิงก์ ให้แสดงในรูปแบบ '[ชื่อลิงก์](URL)' หรือแทรกในประโยคอย่างแนบเนียน\n"
                     . "- หากมีเบอร์โทรศัพท์ ให้ระบุเป็นลิงก์โทร เช่น '[056-009-667](tel:056009667)' เพื่อให้ผู้ใช้แตะโทรออกได้ทันที\n"
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
                        'unread_user_count' => (int)$session->unread_user_count + 1
                    ]);

                    return $this->db->table('tb_chat_messages')->where('message_id', $botMsgId)->get()->getRow();
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