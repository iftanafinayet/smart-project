<?php

namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder {
    public function run() {
        $data = [
            [
                'supplier_name'  => 'PT. Sumber Makmur',
                'contact_person' => 'Budi',
                'phone'          => '08123456789',
                'address'        => 'Jl. Industri No. 10',
            ],
        ];
        $this->db->table('suppliers')->insertBatch($data);
    }
}