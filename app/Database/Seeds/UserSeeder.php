<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Matikan foreign key check sementara
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        
        // Bersihkan tabel users agar tidak duplicate username
        $this->db->table('users')->truncate();
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'role_id'   => 1, // Admin (Pastikan di RoleSeeder ID 1 adalah admin)
                'username'  => 'admin',
                'password'  => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name' => 'Administrator Utama',
            ],
            [
                'role_id'   => 2, // Kasir
                'username'  => 'kasir1',
                'password'  => password_hash('kasir123', PASSWORD_BCRYPT),
                'full_name' => 'Staff Kasir 01',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}