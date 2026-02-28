<?php

namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder {
    public function run() {
        $data = [
            [
                'customer_name'  => 'Umum/Cash',
                'email'          => 'guest@mail.com',
                'phone'          => '0000',
                'address'        => '-',
            ],
        ];
        $this->db->table('customers')->insertBatch($data);
    }
}