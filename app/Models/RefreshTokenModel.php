<?php

namespace App\Models;

use CodeIgniter\Model;

class RefreshTokenModel extends Model
{
    protected $table = 'refresh_tokens';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'token',
        'expires_at',
        'revoked'
    ];

    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function findValidToken($token)
    {
        return $this->where('token', $token)
                    ->where('revoked', false)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }
}