<?php

namespace App\Models;

use CodeIgniter\Model;

class OauthConfigModel extends Model
{
    protected $table            = 'tb_chat_oauth_config';
    protected $primaryKey       = 'config_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'google_client_id',
        'google_client_secret',
        'allowed_domains',
        'auto_register_domain',
        'updated_at',
    ];

    protected $useTimestamps = false;
}