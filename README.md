# Smart POS Enterprise - Warehouse & Retail Core
> Sistem Point of Sales (POS) berbasis web yang dirancang untuk manajemen inventaris dan transaksi organisasi secara real-time.

---

## 🏗️ 1. Arsitektur 3-Tier (Three-Tier Architecture)
Aplikasi ini diimplementasikan menggunakan arsitektur tiga lapis untuk memastikan modularitas, keamanan data, dan skalabilitas sistem:

1.  **Presentation Tier (Frontend)**: 
    * **Teknologi**: HTML5, Tailwind CSS, JavaScript (ES6+), Lucide Icons.
    * **Fungsi**: Antarmuka bagi Kasir, Admin, dan Petugas Gudang untuk berinteraksi dengan sistem secara visual dan responsif.
2.  **Application Tier (Logic)**: 
    * **Teknologi**: CodeIgniter 4 (PHP 8.4).
    * **Fungsi**: Memproses logika bisnis seperti validasi transaksi, pengurangan stok otomatis, dan otentikasi menggunakan JSON Web Token (JWT).
3.  **Data Tier (Storage)**: 
    * **Teknologi**: MySQL/MariaDB.
    * **Fungsi**: Penyimpanan data persisten untuk katalog produk, data karyawan, riwayat penjualan, dan log mutasi stok.

## 📊 2. Schematic Diagram & Logic Alur
Sistem ini menggunakan alur kerja terintegrasi untuk menjaga integritas data organisasi:

* **Pola MVC**: Pemisahan tegas antara **Routes** (URL), **Controller** (Logika), **Model** (Database), dan **View** (Tampilan).
* **Inventory Tracking**: Setiap transaksi di Kasir secara otomatis memotong kolom `current_stock` pada tabel `products` dan mencatatnya sebagai tipe 'Out' di tabel `stock_logs`.
* **Security Layer**: Implementasi filter JWT pada rute API untuk membatasi akses berdasarkan peran (Admin, Kasir, atau Gudang).

## ⚙️ 3. Hubungan Antar Komponen
Aplikasi ini mensinkronkan data antar modul secara real-time:
* **Routes**: Menghubungkan endpoint API (seperti `/api/v1/sales`) dengan Controller terkait.
* **Controller**: Mengelola input, menjalankan transaksi database (`db->transStart`), dan mengembalikan response JSON.
* **View**: Menampilkan data dari Controller dengan estetika modern, sudut melengkung ekstrem (`rounded-[3rem]`), dan tipografi tebal (`font-black`).

## 🎨 4. User Interface (UI) Overview
Desain antarmuka dirancang dengan gaya *modern playful tech*:
* **Dashboard Admin**: Menampilkan grafik tren penjualan (Chart.js) dan kartu statistik pendapatan.
* **Warehouse Panel**: Dilengkapi peringatan stok kritis untuk barang yang stoknya di bawah 5 unit.
* **Katalog & Karyawan**: Manajemen CRUD (Create, Read, Update, Delete) yang bersih dengan umpan balik visual yang interaktif.

## 🚀 5. Instalasi & Konfigurasi

### Prasyarat
* **PHP 8.4** atau lebih baru.
* **Composer** (Dependency Manager).
* **MySQL Server**.

### Langkah-langkah
1.  **Clone Repositori**:
    ```bash
    git clone [https://github.com/username/smart-pos.git](https://github.com/username/smart-pos.git)
    cd smart-pos
    ```
2.  **Instal Dependensi**:
    ```bash
    composer install
    ```
3.  **Konfigurasi Environment**:
    Salin `.env.example` menjadi `.env` dan sesuaikan pengaturan database:
    ```env
    database.default.database = nama_db_anda
    database.default.username = root
    database.default.password = 
    JWT_SECRET = rahasia_pos_anda
    ```
4.  **Database Setup**:
    Pastikan tabel `products`, `sales`, `sale_items`, `users`, dan `stock_logs` sudah sesuai dengan skema SQL.
5.  **Jalankan Aplikasi**:
    ```bash
    php spark serve
    ```
    Buka `http://localhost:8080` pada browser Anda.

---
*Developed by: Nayet Iftanafi