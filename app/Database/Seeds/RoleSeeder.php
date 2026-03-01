<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'role_name' => 'admin'],
            ['id' => 2, 'role_name' => 'kasir'],
            ['id' => 3, 'role_name' => 'gudang'],
        ];

        // Menggunakan replace agar tidak error jika data ID 1 & 2 sudah ada
        foreach ($data as $row) {
            $this->db->table('roles')->replace($row);
        }
    }
}