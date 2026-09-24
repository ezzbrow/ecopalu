<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // 1. Kategori Sampah
        $kategori = [
            [
                'nama_kategori' => 'botol_plastik',
                'coin_value'    => 50,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'kardus',
                'coin_value'    => 30,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'kaleng',
                'coin_value'    => 40,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'besi',
                'coin_value'    => 80,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'botol_kaca',
                'coin_value'    => 25,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($kategori as $k) {
            $existing = $this->db->table('kategori_sampah')->where('nama_kategori', $k['nama_kategori'])->get()->getRow();
            if (! $existing) {
                $this->db->table('kategori_sampah')->insert($k);
            }
        }

        // 2. Akun Default (Admin, Bank Sampah, User)
        $users = [
            [
                'name'       => 'Admin EcoPalu',
                'email'      => 'al1@gmail.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Bank Sampah Kabelotapura',
                'email'      => 'banksampah@ecopalu.com',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'banksampah',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Budi Santoso',
                'email'      => 'user@ecopalu.com',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $u) {
            $existing = $this->db->table('users')->where('email', $u['email'])->get()->getRow();
            if (! $existing) {
                $this->db->table('users')->insert($u);
            }
        }
    }
}
