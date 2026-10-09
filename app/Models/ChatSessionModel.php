<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatSessionModel extends Model
{
    protected $table            = 'tb_chat_sessions';
    protected $primaryKey       = 'session_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_token',
        'user_name',
        'user_tel',
        'assigned_agent_id',
        'department',
        'notes',
        'user_ip',
        'user_agent',
        'telegram_last_msg_id',
        'status',
        'unread_user_count',
        'unread_admin_count',
        'admin_active_at',
        'last_admin_reply_at',
        'is_bot_paused',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}