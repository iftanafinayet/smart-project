<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'        => 1, // Memastikan ID 1 ada untuk relasi SaleSeeder
                'role_id'   => 1, // Admin
                'username'  => 'admin',
                'password'  => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name' => 'Administrator Utama',
            ],
            [
                'id'        => 2,
                'role_id'   => 2, // Kasir
                'username'  => 'kasir1',
                'password'  => password_hash('kasir123', PASSWORD_BCRYPT),
                'full_name' => 'Staff Kasir 01',
            ],
            [
                'id'        => 3,
                'role_id'   => 3, // Kasir
                'username'  => 'gudang',
                'password'  => password_hash('gudang123', PASSWORD_BCRYPT),
                'full_name' => 'Warehouse',
            ]
        ];

        $this->db->table('users')->emptyTable(); 
        $this->db->table('users')->insertBatch($data);
    }
}