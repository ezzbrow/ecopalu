<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiCoinModel extends Model
{
    protected $table = 'transaksi_coin';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'user_id',
        'kategori_sampah_id',
        'berat',
        'total_coin'
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}