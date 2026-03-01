<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class SaleSeeder extends Seeder {
    public function run() {
        $db = \Config\Database::connect();
        // Ambil ID user yang ada untuk menghindari Foreign Key error
        $user = $db->table('users')->select('id')->limit(1)->get()->getRow();
        if (!$user) die("Jalankan UserSeeder dahulu!");

        $db->table('sales')->emptyTable();
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data[] = [
                'invoice_number' => 'INV-' . date('Ymd') . rand(100, 999),
                'user_id'        => $user->id,
                'sale_date'      => $date,
                'total_gross'    => 1000000.00,
                'total_net'      => (float)rand(500000, 2000000), // Digunakan grafik
                'payment_status' => 'Paid'
            ];
        }
        $db->table('sales')->insertBatch($data);
    }
}