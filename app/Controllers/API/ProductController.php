<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Services\ProductService;

class ProductController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    public function index()
    {
        return $this->respond($this->service->getAllProducts());
    }

    public function show($id = null)
    {
        $data = $this->service->getProductById($id);
        if (!$data) return $this->failNotFound('Product not found');
        return $this->respond($data);
    }

    public function create()
    {
        // 1. Rules Validasi (Termasuk Image)
        $rules = [
            'product_name' => 'required|min_length[3]|is_unique[products.product_name]',
            'category_id'  => 'required|is_not_unique[categories.id]',
            'price'        => 'required|numeric',
            'stock'        => 'required|integer',
            'image'        => 'uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // 2. Handle Upload Gambar
        $img = $this->request->getFile('image');
        $newName = $img->getRandomName();
        $img->move(ROOTPATH . 'public/uploads', $newName);

        // 3. Ambil data dari form-data (bukan getJSON karena ada file)
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'category_id'  => $this->request->getPost('category_id'),
            'price'        => $this->request->getPost('price'),
            'stock'        => $this->request->getPost('stock'),
            'description'  => $this->request->getPost('description'),
            'image'        => $newName
        ];

        $this->service->createProduct($data);
        return $this->respondCreated(['message' => 'Product created with image', 'data' => $data]);
    }

    public function update($id = null)
    {
        if (!$this->service->getProductById($id)) return $this->failNotFound('Product not found');

        // Validasi Update
        $rules = [
            'product_name' => "min_length[3]|is_unique[products.product_name,id,$id]",
            'price'        => 'numeric',
            'image'        => 'permit_empty|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) return $this->failValidationErrors($this->validator->getErrors());

        // Ambil data input
        $data = $this->request->getRawInput(); // Untuk method PUT
        
        $this->service->updateProduct($id, $data);
        return $this->respond(['message' => 'Product updated']);
    }

    public function delete($id = null)
    {
        if (!$this->service->getProductById($id)) return $this->failNotFound('Product not found');
        
        $this->service->deleteProduct($id);
        return $this->respondDeleted(['message' => 'Product deleted successfully']);
    }
}