<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataMiningSampleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Dapatkan kategori sampah ID
        $kategoriRows = $db->table('kategori_sampah')->get()->getResultArray();
        if (empty($kategoriRows)) {
            return;
        }
        $katMap = [];
        foreach ($kategoriRows as $k) {
            $katMap[$k['nama_kategori']] = (int) $k['id'];
        }

        // 2. Buat nasabah sampel
        $sampleUsers = [
            ['name' => 'Siti Rahmawati', 'email' => 'siti@ecopalu.id'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@ecopalu.id'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@ecopalu.id'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky@ecopalu.id'],
            ['name' => 'Nurul Hidayah', 'email' => 'nurul@ecopalu.id'],
            ['name' => 'Bayu Setiawan', 'email' => 'bayu@ecopalu.id'],
            ['name' => 'Indah Permata', 'email' => 'indah@ecopalu.id'],
            ['name' => 'Fajar Nugraha', 'email' => 'fajar@ecopalu.id'],
            ['name' => 'Mega Utami', 'email' => 'mega@ecopalu.id'],
            ['name' => 'Hendra Wijaya', 'email' => 'hendra@ecopalu.id'],
            ['name' => 'Rina Marlina', 'email' => 'rina@ecopalu.id'],
            ['name' => 'Dedi Suryadi', 'email' => 'dedi@ecopalu.id'],
        ];

        $userIds = [];
        $pwdHash = password_hash('password123', PASSWORD_DEFAULT);
        $now = date('Y-m-d H:i:s');

        // Pastikan user demo default juga ada di list
        $defaultUser = $db->table('users')->where('email', 'user@ecopalu.com')->get()->getRow();
        if ($defaultUser) {
            $userIds[] = (int) $defaultUser->id;
        }

        foreach ($sampleUsers as $su) {
            $existing = $db->table('users')->where('email', $su['email'])->get()->getRow();
            if ($existing) {
                $userIds[] = (int) $existing->id;
            } else {
                $db->table('users')->insert([
                    'name'       => $su['name'],
                    'email'      => $su['email'],
                    'password'   => $pwdHash,
                    'role'       => 'user',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-60 days')),
                    'updated_at' => $now,
                ]);
                $userIds[] = (int) $db->insertID();
            }
        }

        // Cek jika penjemputan sudah banyak, tidak perlu insert ulang
        $countPj = $db->table('penjemputan')->countAllResults();
        if ($countPj >= 20) {
            return;
        }

        // 3. Pola Transaksi Penjemputan & Transaksi Coin
        // Pola basket yang realistis untuk Apriori:
        // - botol_plastik sering bersama kaleng
        // - botol_plastik sering bersama kardus
        // - kardus sering bersama botol_kaca
        $transactionsTemplate = [
            // User 0 (Sangat Aktif: banyak transaksi, berat tinggi)
            ['u_idx' => 0, 'days_ago' => 2,  'items' => [['botol_plastik', 12.5], ['kaleng', 4.0], ['kardus', 8.0]]],
            ['u_idx' => 0, 'days_ago' => 9,  'items' => [['botol_plastik', 15.0], ['kaleng', 5.5]]],
            ['u_idx' => 0, 'days_ago' => 16, 'items' => [['botol_plastik', 14.0], ['kardus', 10.0], ['kaleng', 3.5]]],
            ['u_idx' => 0, 'days_ago' => 23, 'items' => [['botol_plastik', 18.0], ['kaleng', 6.0]]],

            // User 1 (Sangat Aktif)
            ['u_idx' => 1, 'days_ago' => 3,  'items' => [['botol_plastik', 20.0], ['kardus', 15.0], ['kaleng', 7.0]]],
            ['u_idx' => 1, 'days_ago' => 10, 'items' => [['botol_plastik', 11.0], ['kaleng', 4.2]]],
            ['u_idx' => 1, 'days_ago' => 17, 'items' => [['besi', 25.0], ['kardus', 12.0]]],
            ['u_idx' => 1, 'days_ago' => 24, 'items' => [['botol_plastik', 16.5], ['kaleng', 5.0], ['kardus', 9.0]]],

            // User 2 (Aktif / Menengah)
            ['u_idx' => 2, 'days_ago' => 5,  'items' => [['botol_plastik', 8.5], ['kardus', 6.0]]],
            ['u_idx' => 2, 'days_ago' => 19, 'items' => [['botol_plastik', 7.0], ['kaleng', 2.5]]],
            ['u_idx' => 2, 'days_ago' => 33, 'items' => [['kardus', 11.0], ['botol_kaca', 5.0]]],

            // User 3 (Aktif / Menengah)
            ['u_idx' => 3, 'days_ago' => 6,  'items' => [['botol_plastik', 10.0], ['kaleng', 4.0]]],
            ['u_idx' => 3, 'days_ago' => 20, 'items' => [['botol_plastik', 9.0], ['kardus', 7.5]]],

            // User 4 (Aktif / Menengah)
            ['u_idx' => 4, 'days_ago' => 7,  'items' => [['kardus', 8.0], ['botol_kaca', 6.0]]],
            ['u_idx' => 4, 'days_ago' => 21, 'items' => [['botol_plastik', 6.5], ['kaleng', 3.0]]],

            // User 5 (Sedang)
            ['u_idx' => 5, 'days_ago' => 12, 'items' => [['botol_plastik', 7.0], ['kardus', 5.0]]],
            ['u_idx' => 5, 'days_ago' => 26, 'items' => [['botol_plastik', 8.0], ['kaleng', 3.5]]],

            // User 6 (Sedang)
            ['u_idx' => 6, 'days_ago' => 14, 'items' => [['kardus', 9.0], ['botol_kaca', 4.5]]],
            ['u_idx' => 6, 'days_ago' => 28, 'items' => [['botol_plastik', 5.5], ['kardus', 4.0]]],

            // User 7 (Pasif / Jarang)
            ['u_idx' => 7, 'days_ago' => 35, 'items' => [['botol_plastik', 4.0]]],

            // User 8 (Pasif / Jarang)
            ['u_idx' => 8, 'days_ago' => 42, 'items' => [['kardus', 5.0]]],

            // User 9 (Pasif / Lama tidak setor)
            ['u_idx' => 9, 'days_ago' => 50, 'items' => [['botol_plastik', 3.5], ['kaleng', 1.5]]],

            // User 10 (Baru / Sedikit)
            ['u_idx' => 10, 'days_ago' => 4, 'items' => [['botol_plastik', 5.0], ['kaleng', 2.0]]],

            // User 11 (Pasif)
            ['u_idx' => 11, 'days_ago' => 45, 'items' => [['botol_kaca', 6.0]]],
        ];

        foreach ($transactionsTemplate as $tx) {
            $uId = $userIds[$tx['u_idx'] % count($userIds)];
            $dt = date('Y-m-d H:i:s', strtotime("-{$tx['days_ago']} days"));
            $tglJemput = date('Y-m-d', strtotime("-{$tx['days_ago']} days"));

            foreach ($tx['items'] as $item) {
                $katNama = $item[0];
                $berat = (float) $item[1];
                $katId = $katMap[$katNama] ?? 1;

                // Ambil nilai koin per kg
                $kategoriInfo = $db->table('kategori_sampah')->where('id', $katId)->get()->getRow();
                $coinPerKg = $kategoriInfo ? (int) $kategoriInfo->coin_value : 50;
                $totalCoin = (int) round($berat * $coinPerKg);

                // Insert penjemputan
                $db->table('penjemputan')->insert([
                    'user_id'            => $uId,
                    'kategori_sampah_id' => $katId,
                    'berat'              => $berat,
                    'tanggal_jemput'     => $tglJemput,
                    'alamat'             => 'Jl. Tadulako No. ' . rand(10, 99) . ', Kota Palu',
                    'latitude'           => -0.8917000 + (rand(-100, 100) / 10000),
                    'longitude'          => 119.8707000 + (rand(-100, 100) / 10000),
                    'status'             => 'selesai',
                    'verified_by'        => 1,
                    'verified_at'        => $dt,
                    'confirmed_by'       => 2,
                    'confirmed_at'       => $dt,
                    'coin_award'         => $totalCoin,
                    'coin_awarded_at'    => $dt,
                    'finalized_by'       => 1,
                    'created_at'         => $dt,
                    'updated_at'         => $dt,
                ]);

                // Insert transaksi_coin
                $db->table('transaksi_coin')->insert([
                    'user_id'            => $uId,
                    'kategori_sampah_id' => $katId,
                    'berat'              => $berat,
                    'total_coin'         => $totalCoin,
                    'created_at'         => $dt,
                    'updated_at'         => $dt,
                ]);
            }
        }
    }
}
