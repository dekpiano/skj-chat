<?php

namespace App\Models;

use CodeIgniter\Model;

class AiConfigModel extends Model
{
    protected $table            = 'tb_chat_ai_config';
    protected $primaryKey       = 'ai_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ai_provider',
        'ai_api_key',
        'ai_model',
        'ai_system_prompt',
        'ai_status',
        'ai_temperature',
        'ai_max_tokens',
        'updated_at',
    ];

    protected $useTimestamps = false;
}