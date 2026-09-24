<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilAsosiasiModel extends Model
{
    protected $table = 'hasil_asosiasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'antecedents',
        'consequents',
        'support',
        'confidence',
        'lift',
        'keterangan',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
}
