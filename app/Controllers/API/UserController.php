<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    public function me()
    {
        $userData = $this->request->user ?? null; 
        
        return $this->respond($userData);
    }
}