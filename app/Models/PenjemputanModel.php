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
        'tanggal_jemput',
        'alamat',
        'status'
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}