<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before($request, $arguments = null)
    {
        $userRole = service('request')->userRole ?? null;

        if (!$userRole || !in_array($userRole, $arguments)) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['message' => 'Forbidden']);
        }
    }

    public function after($request, $response, $arguments = null) {}
}