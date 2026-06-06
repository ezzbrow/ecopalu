<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriSampahModel extends Model
{
    protected $table = 'kategori_sampah';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nama_kategori',
        'coin_value'
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}