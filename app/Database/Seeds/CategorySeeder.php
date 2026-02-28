<?php

namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder {
    public function run() {
        $data = [
            ['category_name' => 'Makanan'],
            ['category_name' => 'Minuman'],
            ['category_name' => 'Alat Tulis'],
        ];
        $this->db->table('categories')->insertBatch($data);
    }
}