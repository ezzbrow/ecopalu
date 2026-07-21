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
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Hitung saldo coin user (running total dari semua transaksi).
     * Saldo ini yang jadi dasar pengajuan pencairan.
     */
    public function saldoCoin(int $userId): int
    {
        $row = $this
            ->selectSum('total_coin')
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->get()
            ->getRow();
        return (int) ($row->total_coin ?? 0);
    }
}