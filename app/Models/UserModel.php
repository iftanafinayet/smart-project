<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Perbaikan: Tambahkan 'full_name' agar bisa di-input
    protected $allowedFields    = ['username', 'password', 'full_name', 'role_id'];

    // Perbaikan: Set false jika di database tidak ada created_at/updated_at
    protected $useTimestamps = false; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}