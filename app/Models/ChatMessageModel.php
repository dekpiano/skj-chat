<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table            = 'tb_chat_messages';
    protected $primaryKey       = 'message_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_id',
        'sender_type',
        'sender_name',
        'agent_id',
        'message',
        'attachment_url',
        'attachment_type',
        'is_bot',
        'telegram_msg_id',
        'is_read',
        'created_at',
    ];

    protected $useTimestamps = false;
}