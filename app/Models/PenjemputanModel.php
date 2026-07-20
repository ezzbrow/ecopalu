<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjemputanModel extends Model
{
    protected $table = 'penjemputan';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id',
        'kategori_sampah_id',
        'berat',
        'tanggal_jemput',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'verified_by',
        'verified_at',
        'alasan_penolakan',
        'confirmed_by',
        'confirmed_at',
        'coin_award',
        'coin_awarded_at',
        'finalized_by',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}