<?php

namespace App\Models;

use CodeIgniter\Model;

class CannedReplyModel extends Model
{
    protected $table            = 'tb_chat_canned_replies';
    protected $primaryKey       = 'reply_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shortcut',
        'title',
        'message',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}