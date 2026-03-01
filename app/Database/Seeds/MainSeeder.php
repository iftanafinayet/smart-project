<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan Foreign Key Checks agar TRUNCATE tidak error karena relasi
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // 2. Kosongkan tabel (TRUNCATE)
        $this->db->table('users')->truncate(); 
        $this->db->table('customers')->truncate();
        $this->db->table('products')->truncate();
        $this->db->table('sales')->truncate();
        $this->db->table('employees')->truncate();
        $this->db->table('roles')->truncate();
        $this->db->table('categories')->truncate();

        // 3. Nyalakan kembali Foreign Key Checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // 4. Seed Roles (Wajib karena ada role_id di tabel users)
        $this->db->table('roles')->insertBatch([
            ['role_name' => 'Admin'],
            ['role_name' => 'Kasir'],
        ]);

        // 5. Seed Users (Admin & Kasir) - Sesuai kolom: role_id, username, password, full_name
        $userData = [
            [
                'role_id'   => 1, // Admin
                'username'  => 'admin_baru',
                'password'  => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name' => 'Administrator Utama'
            ],
            [
                'role_id'   => 2, // Kasir
                'username'  => 'kasir_test',
                'password'  => password_hash('kasir123', PASSWORD_BCRYPT),
                'full_name' => 'Staff Kasir Testing'
            ]
        ];
        $this->db->table('users')->insertBatch($userData);

        // 6. Seed Categories (Wajib karena ada category_id di tabel products)
        $this->db->table('categories')->insertBatch([
            ['category_name' => 'Minuman'],
            ['category_name' => 'Makanan'],
            ['category_name' => 'Sembako'],
        ]);

        // 7. Seed Customers
        $customerData = [
            ['customer_name' => 'Budi Susanto', 'email' => 'budi@gmail.com', 'phone' => '08123456789'],
            ['customer_name' => 'Siti Aminah', 'email' => 'siti@gmail.com', 'phone' => '08776543210'],
        ];
        $this->db->table('customers')->insertBatch($customerData);

        // 8. Seed Products (Menggunakan selling_price dan category_id)
        $productData = [
            [
                'product_name'  => 'Kopi Hitam Arabika',
                'sku'           => 'PRD001',
                'selling_price' => 50000, 
                'current_stock' => 5, // KRITIS
                'min_stock'     => 10,
                'category_id'   => 1 // ID Minuman
            ],
            [
                'product_name'  => 'Indomie Goreng',
                'sku'           => 'PRD002',
                'selling_price' => 3500, 
                'current_stock' => 2, // KRITIS
                'min_stock'     => 20,
                'category_id'   => 2 // ID Makanan
            ],
            [
                'product_name'  => 'Beras Setra Ramos 5kg',
                'sku'           => 'PRD003',
                'selling_price' => 75000, 
                'current_stock' => 50, // AMAN
                'min_stock'     => 10,
                'category_id'   => 3 // ID Sembako
            ],
        ];
        $this->db->table('products')->insertBatch($productData);

        // 9. Seed Sales History (7 Hari Terakhir untuk Grafik Dashboard)
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $this->db->table('sales')->insert([
                'invoice_number' => "INV-202603-" . (100 + $i),
                'customer_id'    => 1, // ID dari Budi Susanto
                'user_id'        => 1, // ID dari admin_baru
                'sale_date'      => $date,
                'total_net'      => rand(100000, 500000), // Random amount untuk variasi grafik
                'payment_status' => 'Paid'
            ]);
        }

        // 10. Seed Employees
        $this->db->table('employees')->insert([
            'user_id'    => 1, // ID dari admin_baru
            'nik'        => 'EMP2026001',
            'position'   => 'Store Manager',
            'join_date'  => '2026-01-01'
        ]);
    }
}