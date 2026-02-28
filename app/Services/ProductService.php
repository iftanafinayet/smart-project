<?php

namespace App\Services;

use App\Repository\ProductRepository;
use App\Models\ProductModel;

class ProductService
{
    protected $repo;
    protected $model;

    public function __construct()
    {
        $this->repo = new ProductRepository();
        $this->model = new ProductModel();
    }

    public function getAllProducts()
    {
        return $this->repo->findAllWithCategory();
    }

    public function getProductById($id)
    {
        return $this->model->find($id);
    }

    public function createProduct($data)
    {
        return $this->model->insert($data);
    }

    public function updateProduct($id, $data)
{   
    return $this->model->update($id, $data);
}

public function deleteProduct($id)
{
    return $this->model->delete($id);
}
}