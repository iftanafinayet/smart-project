<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class UserController extends BaseController
{
    use ResponseTrait;

    // GET all users
    public function index()
    {
        $model = new UserModel();
        // Mengambil data sesuai kolom di gambar
        $users = $model->select('id, username, full_name, role_id')->findAll();
        return $this->respond(['data' => $users]);
    }

    // POST create user (Admin Only)
    public function create()
    {
        $model = new UserModel();
        $data = $this->request->getJSON(true);

        // Hashing password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Karyawan berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan karyawan');
    }

    // PUT update user (Admin Only)
    public function update($id = null)
    {
        $model = new UserModel();
        $data = $this->request->getJSON(true);
        
        // Hashing password jika diubah
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        if ($model->update($id, $data)) {
            return $this->respond(['message' => 'Karyawan berhasil diupdate']);
        }
        return $this->failNotFound('Karyawan tidak ditemukan');
    }

    // DELETE user (Admin Only)
    public function delete($id = null)
    {
        $model = new UserModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['message' => 'Karyawan berhasil dihapus']);
        }
        return $this->failNotFound('Karyawan tidak ditemukan');
    }
}