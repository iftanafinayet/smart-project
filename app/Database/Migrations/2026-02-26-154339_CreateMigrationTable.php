<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnterpriseTables extends Migration
{
    public function up()
    {
        // ==========================================
        // Tabel yang sudah ada (Struktur kamu)
        // ==========================================

        // 1. Roles
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_name'      => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles');

        // 2. Users
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'username'       => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'full_name'      => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');

        // 3. Categories
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');

        // 4. Products
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'sku'            => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'product_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'unit'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'purchase_price' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'selling_price'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'current_stock'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'min_stock'      => ['type' => 'INT', 'constraint' => 11, 'default' => 10],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('products');

        // 5. Suppliers
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'contact_person' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'        => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('suppliers');

        // 6. Purchase Orders
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'po_number'      => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'po_date'        => ['type' => 'DATE'],
            'total_cost'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'status'         => ['type' => 'ENUM', 'constraint' => ['Pending', 'Received', 'Cancelled'], 'default' => 'Pending'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('purchase_orders');

        // 7. Purchase Order Items
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'po_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'            => ['type' => 'INT', 'constraint' => 11],
            'cost_per_unit'  => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_order_items');

        // 8. Customers
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'customer_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'        => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');

        // 9. Sales
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'customer_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // Karyawan yang input
            'sale_date'      => ['type' => 'DATE'],
            'total_gross'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'discount_total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'total_net'      => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'payment_status' => ['type' => 'ENUM', 'constraint' => ['Unpaid', 'Paid', 'Partial'], 'default' => 'Unpaid'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('sales');

        // 10. Sale Items
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sale_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'            => ['type' => 'INT', 'constraint' => 11],
            'price_at_sale'  => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_items');

        // 11. Stock Logs
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type'           => ['type' => 'ENUM', 'constraint' => ['In', 'Out']],
            'qty'            => ['type' => 'INT', 'constraint' => 11],
            'reference_no'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_logs');

        // ==========================================
        // TAMBAHAN: Tabel Baru untuk Integrasi
        // ==========================================

        // 12. Departments (HR)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'dept_name'      => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departments');

        // 13. Employees (HR) - Menghubungkan user ke HR
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // Link ke login user
            'dept_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nik'            => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'position'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'join_date'      => ['type' => 'DATE'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('dept_id', 'departments', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('employees');

        // 14. Finance Transactions (Finance) - Integrasi Sales & Purchase
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'type'           => ['type' => 'ENUM', 'constraint' => ['Income', 'Expense']],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'description'    => ['type' => 'TEXT'],
            'transaction_date'=> ['type' => 'DATETIME'],
            'reference_no'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // Invoice no atau PO no
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('finance_transactions');
    }

    public function down()
    {
        // Matikan foreign key check supaya bisa drop tanpa error
        $this->db->disableForeignKeyChecks();

        // Drop Tabel Baru
        $this->forge->dropTable('finance_transactions', true);
        $this->forge->dropTable('employees', true);
        $this->forge->dropTable('departments', true);

        // Drop Tabel Awal
        $this->forge->dropTable('stock_logs', true);
        $this->forge->dropTable('sale_items', true);
        $this->forge->dropTable('sales', true);
        $this->forge->dropTable('customers', true);
        $this->forge->dropTable('purchase_order_items', true);
        $this->forge->dropTable('purchase_orders', true);
        $this->forge->dropTable('suppliers', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('roles', true);

        $this->db->enableForeignKeyChecks();
    }
}