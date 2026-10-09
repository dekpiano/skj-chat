<?php

namespace App\Models;

use CodeIgniter\Model;

class KnowledgeModel extends Model
{
    protected $table            = 'tb_chat_ai_knowledge';
    protected $primaryKey       = 'knowledge_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'source_type',
        'source_url',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'content',
        'char_count',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}