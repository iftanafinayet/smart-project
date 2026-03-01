<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'category_id'    => 1,
                'sku'            => 'STK-001',
                'product_name'   => 'Sticker Pack CCIT FTUI',
                'unit'           => 'Pack',
                'purchase_price' => 10000,
                'selling_price'  => 15000,
                'current_stock'  => 5, // Stok rendah untuk memicu alert dashboard
                'min_stock'      => 10
            ],
            [
                'category_id'    => 1,
                'sku'            => 'PIN-001',
                'product_name'   => 'Enamel Pin Logo Teknik',
                'unit'           => 'Pcs',
                'purchase_price' => 15000,
                'selling_price'  => 25000,
                'current_stock'  => 50,
                'min_stock'      => 10
            ],
            [
                'category_id'    => 1,
                'sku'            => 'KCH-002',
                'product_name'   => 'Acrylic Keychain Code.Debug.Repeat',
                'unit'           => 'Pcs',
                'purchase_price' => 12000,
                'selling_price'  => 20000,
                'current_stock'  => 8, // Stok rendah
                'min_stock'      => 10
            ],
            [
                'category_id'    => 2,
                'sku'            => 'JKT-001',
                'product_name'   => 'Jahim CCIT FTUI Orange',
                'unit'           => 'Pcs',
                'purchase_price' => 180000,
                'selling_price'  => 250000,
                'current_stock'  => 15,
                'min_stock'      => 5
            ]
        ];

        // Memasukkan data ke tabel products sesuai struktur diagram
        $this->db->table('products')->insertBatch($data);
    }
}