<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Jika ada parameter role (misal auth:user atau auth:creator)
        if ($params) {
            $requiredRole = $params[0];
            $userRole = $session->get('role');

            // IZINKAN: Kreator boleh mengakses menu User (dashboard, profile, dsb)
            // Namun User TIDAK boleh mengakses menu Kreator
            if ($userRole === 'creator' && $requiredRole === 'user') {
                return;
            }

            if ($userRole !== $requiredRole) {
                // Jika user mencoba akses dashboard creator
                if ($userRole === 'user' && $requiredRole === 'creator') {
                    return redirect()->to(base_url('dashboard'))->with('error', 'Silakan daftar jadi Kreator untuk mengakses halaman ini.');
                }
                
                // Fallback default
                return redirect()->to(base_url('dashboard'))->with('error', 'Akses ditolak.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $params = null)
    {
        // Do something here
    }
}
