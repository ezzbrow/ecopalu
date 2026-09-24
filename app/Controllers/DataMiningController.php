<?php

namespace App\Controllers;

use App\Models\HasilKlasterModel;
use App\Models\HasilAsosiasiModel;
use App\Models\PenjemputanModel;

class DataMiningController extends BaseController
{
    protected $klasterModel;
    protected $asosiasiModel;

    public function __construct()
    {
        $this->klasterModel = new HasilKlasterModel();
        $this->asosiasiModel = new HasilAsosiasiModel();
    }

    /**
     * Tampilan utama Data Mining Center di Dashboard Admin.
     * Route: GET /admin/data-mining
     */
    public function index()
    {
        // 1. Ambil hasil K-Means dari tabel hasil_klaster
        $klasterData = $this->klasterModel
            ->orderBy('cluster_id', 'ASC')
            ->orderBy('total_berat', 'DESC')
            ->findAll();

        // 2. Ambil hasil Apriori dari tabel hasil_asosiasi
        $asosiasiData = $this->asosiasiModel
            ->orderBy('lift', 'DESC')
            ->orderBy('confidence', 'DESC')
            ->findAll();

        // 3. Ringkasan Klaster untuk visualisasi / statistik
        $clusterSummary = [];
        $totalNasabah = count($klasterData);
        $lastRunTime = null;

        foreach ($klasterData as $k) {
            $label = $k['cluster_label'] ?? 'Cluster ' . $k['cluster_id'];
            if (! isset($clusterSummary[$label])) {
                $clusterSummary[$label] = [
                    'count'        => 0,
                    'total_berat'  => 0,
                    'total_poin'   => 0,
                    'cluster_id'   => $k['cluster_id'],
                    'rekomendasi'  => $k['rekomendasi'],
                ];
            }
            $clusterSummary[$label]['count']++;
            $clusterSummary[$label]['total_berat'] += (float) $k['total_berat'];
            $clusterSummary[$label]['total_poin'] += (int) $k['total_poin'];

            if (! $lastRunTime || strtotime($k['updated_at']) > strtotime($lastRunTime)) {
                $lastRunTime = $k['updated_at'];
            }
        }

        // 4. Data transaksi mentah di MySQL (Langkah 1-4)
        $db = \Config\Database::connect();
        $rawTxCount = $db->table('penjemputan')->where('status', 'selesai')->countAllResults();
        $rawWeight = $db->table('penjemputan')->where('status', 'selesai')->selectSum('berat')->get()->getRow()->berat ?? 0;
        $rawPoints = $db->table('penjemputan')->where('status', 'selesai')->selectSum('coin_award')->get()->getRow()->coin_award ?? 0;

        $data = [
            'title'          => 'Data Mining Center — Admin EcoPalu',
            'klaster'        => $klasterData,
            'asosiasi'       => $asosiasiData,
            'clusterSummary' => $clusterSummary,
            'totalNasabah'   => $totalNasabah,
            'totalRules'     => count($asosiasiData),
            'lastRunTime'    => $lastRunTime,
            'rawStats'       => [
                'txCount' => (int) $rawTxCount,
                'weight'  => round((float) $rawWeight, 2),
                'points'  => (int) $rawPoints,
            ]
        ];

        return view('admin/data_mining', $data);
    }

    /**
     * Jalankan eksekusi skrip Python Data Mining dari dashboard.
     * Route: POST /admin/data-mining/run
     */
    public function run()
    {
        $pythonScript = ROOTPATH . 'data-mining' . DIRECTORY_SEPARATOR . 'run_all.py';
        
        // Eksekusi skrip python
        $output = [];
        $returnVar = 0;
        
        $command = "python \"" . $pythonScript . "\" 2>&1";
        exec($command, $output, $returnVar);

        $outputText = implode("\n", $output);

        if ($returnVar === 0) {
            return redirect()->to('/admin/data-mining')
                ->with('success', 'Skrip Data Mining Python (K-Means & Apriori) berhasil dijalankan! Hasil klaster dan aturan asosiasi telah diperbarui di database.');
        }

        return redirect()->to('/admin/data-mining')
            ->with('error', 'Gagal menjalankan skrip Python. Output: ' . substr($outputText, 0, 300));
    }
}
