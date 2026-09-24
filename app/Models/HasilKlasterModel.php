<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilKlasterModel extends Model
{
    protected $table = 'hasil_klaster';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'nama_nasabah',
        'email',
        'frekuensi',
        'total_berat',
        'total_poin',
        'recency_hari',
        'cluster_id',
        'cluster_label',
        'rekomendasi',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
}
