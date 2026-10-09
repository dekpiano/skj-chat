<?php

namespace App\Models;

use CodeIgniter\Model;

class AgentModel extends Model
{
    protected $table            = 'tb_chat_agents';
    protected $primaryKey       = 'agent_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'google_id',
        'email',
        'fullname',
        'avatar',
        'remember_token',
        'role',
        'status',
        'online_status',
        'assigned_count',
        'last_login_at',
        'last_active_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}