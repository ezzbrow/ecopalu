<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PencairanModel — agregasi tabel `pencairan_reward` untuk Dashboard Admin.
 *
 * Tabel `pencairan_reward` sudah ada dari migration 000007.
 * Kolom nominal_rupiah masih NULL (TODO rate coin→rupiah final),
 * jadi grafik pakai SUM(nominal_coin) dulu.
 */
class PencairanModel extends Model
{
    protected $table         = 'pencairan_reward';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowedFields = [
        'user_id',
        'nominal_coin',
        'nominal_rupiah',
        'jenis_ewallet',
        'nomor_ewallet',
        'status',
        'alasan_penolakan',
        'tanggal_transfer',
    ];

    /**
     * Hitung total pencairan yang berhasil (semua waktu, semua nominal_coin).
     * Untuk badge/info card.
     */
    public function totalCoinBerhasil(): int
    {
        $row = $this
            ->selectSum('nominal_coin')
            ->where('status', 'berhasil')
            ->where('deleted_at', null)
            ->get()
            ->getRow();
        return (int) ($row->nominal_coin ?? 0);
    }

    /**
     * Total record (untuk empty state check).
     */
    public function totalRecord(): int
    {
        return $this->where('deleted_at', null)->countAllResults();
    }

    /**
     * Ambil data 7 hari terakhir untuk grafik.
     * Return: array of ['tanggal' => 'Y-m-d', 'total_coin' => int] untuk 7 hari berturut-turut.
     * Hari tanpa transaksi = 0 (default).
     */
    public function getLast7Days(): array
    {
        // Bangun 7 hari terakhir (H-6 sampai H-0) dengan default 0
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = new \DateTime("-{$i} days");
            $days[$d->format('Y-m-d')] = 0;
        }

        // Query agregat untuk 7 hari terakhir
        $startDate = (new \DateTime('-6 days'))->format('Y-m-d');
        $results = $this
            ->select("DATE(tanggal_transfer) AS hari, SUM(nominal_coin) AS total_coin", false)
            ->where('status', 'berhasil')
            ->where('tanggal_transfer >=', $startDate)
            ->where('deleted_at', null)
            ->groupBy('DATE(tanggal_transfer)')
            ->orderBy('hari', 'ASC')
            ->get()
            ->getResultArray();

        // Merge ke template 7 hari
        foreach ($results as $r) {
            $hari = is_string($r['hari']) ? substr($r['hari'], 0, 10) : null;
            if ($hari && isset($days[$hari])) {
                $days[$hari] = (int) ($r['total_coin'] ?? 0);
            }
        }

        // Format output: [['tanggal' => '2026-07-15', 'label' => '15 Jul', 'total_coin' => 1000], ...]
        $out = [];
        foreach ($days as $date => $total) {
            $out[] = [
                'tanggal'    => $date,
                'label'      => (new \DateTime($date))->format('d M'),
                'total_coin' => $total,
            ];
        }
        return $out;
    }
}