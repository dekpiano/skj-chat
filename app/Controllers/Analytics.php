<?php

namespace App\Controllers;

class Analytics extends BaseController
{
    public function index()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $totalChats    = $this->db->table('tb_chat_sessions')->countAllResults();
        $todayChats    = $this->db->table('tb_chat_sessions')->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $totalMessages = $this->db->table('tb_chat_messages')->countAllResults();
        $botReplies    = $this->db->table('tb_chat_messages')->where('is_bot', 1)->countAllResults();
        $adminReplies  = $this->db->table('tb_chat_messages')->where('sender_type', 'admin')->countAllResults();

        // 7-day chat volume
        $dailyChats = $this->db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM tb_chat_sessions 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
            GROUP BY DATE(created_at) 
            ORDER BY date ASC
        ")->getResult();

        // Agent performance
        $agentStats = $this->db->query("
            SELECT a.agent_id, a.fullname, a.avatar, COUNT(m.message_id) as total_replies
            FROM tb_chat_agents a
            LEFT JOIN tb_chat_messages m ON m.agent_id = a.agent_id
            GROUP BY a.agent_id
            ORDER BY total_replies DESC
        ")->getResult();

        $data = [
            'title'         => 'รายงานและสถิติการสนทนา (Live Chat Analytics)',
            'activeMenu'    => 'analytics',
            'totalChats'    => $totalChats,
            'todayChats'    => $todayChats,
            'totalMessages' => $totalMessages,
            'botReplies'    => $botReplies,
            'adminReplies'  => $adminReplies,
            'dailyChats'    => $dailyChats,
            'agentStats'    => $agentStats,
        ];

        return view('analytics/index', array_merge($this->data, $data));
    }
}