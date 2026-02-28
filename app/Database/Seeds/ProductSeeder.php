<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan cek foreign key agar bisa mengosongkan tabel
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        
        // 2. Kosongkan tabel produk sebelum diisi ulang
        $this->db->table('products')->truncate();
        
        // 3. Hidupkan kembali cek foreign key
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'category_id'    => 1,
                'sku'            => 'BRG001',
                'product_name'   => 'Indomie Goreng',
                'unit'           => 'Pcs',
                'purchase_price' => 2500,
                'selling_price'  => 3000,
                'current_stock'  => 100,
                'min_stock'      => 10,
            ],
            [
                'category_id'    => 2,
                'sku'            => 'BRG002',
                'product_name'   => 'Aqua 600ml',
                'unit'           => 'Botol',
                'purchase_price' => 3000,
                'selling_price'  => 4500,
                'current_stock'  => 50,
                'min_stock'      => 5,
            ],
        ];

        $this->db->table('products')->insertBatch($data);
    }
}