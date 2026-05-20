<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiAuth implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            $response = Services::response();
            return $response->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized. Please login first.',
                'code'    => 401
            ])->setStatusCode(401);
        }

        if ($params) {
            $requiredRole = $params[0];
            $userRole = $session->get('role');

            // IZINKAN: Kreator boleh mengakses menu User
            if ($userRole === 'creator' && $requiredRole === 'user') {
                return;
            }

            if ($userRole !== $requiredRole) {
                $response = Services::response();
                return $response->setJSON([
                    'status'  => 'error',
                    'message' => 'Forbidden. Access denied.',
                    'code'    => 403
                ])->setStatusCode(403);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $params = null)
    {
        // Do something here
    }
}
