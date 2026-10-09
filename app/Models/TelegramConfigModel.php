<?php

namespace App\Models;

use CodeIgniter\Model;

class TelegramConfigModel extends Model
{
    protected $table            = 'tb_telegram_config';
    protected $primaryKey       = 'telegram_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_chat_title',
        'telegram_status',
        'updated_at',
    ];

    protected $useTimestamps = false;
}