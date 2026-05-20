<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn') && session()->get('role') === 'creator') {
            return redirect()->to(base_url('creator/dashboard'));
        }

        $data = [
            'title' => 'Login Kreator - NusaShare',
        ];
        return view('auth/kreator_login', $data);
    }

    public function attempt()
    {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if user is a creator
            if ($user['role'] !== 'creator') {
                return redirect()->back()->withInput()->with('error', 'Akun Anda tidak terdaftar sebagai Kreator.');
            }

            $sessionData = [
                'userId'     => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ];

            session()->set($sessionData);

            return redirect()->to(base_url('creator/dashboard'))->with('success', 'Selamat datang kembali, Kreator ' . $user['username'] . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
    }

    public function register()
    {
        // Check if logged in as standard user
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan masuk sebagai user terlebih dahulu untuk mendaftar sebagai Kreator.');
        }

        // If already a creator, redirect to creator dashboard
        if (session()->get('role') === 'creator') {
            return redirect()->to(base_url('creator/dashboard'));
        }

        $data = [
            'title' => 'Daftar Kreator - NusaShare',
        ];
        return view('auth/kreator_register', $data);
    }

    public function store()
    {
        $userModel = new UserModel();
        $profileModel = new \App\Models\CreatorProfileModel();
        $session = session();

        // STRICT CHECK: Must be logged in as user first
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        }

        $rules = [
            'display_name' => 'required|min_length[3]|max_length[50]',
            'bio'          => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // UPGRADE CURRENT USER
        $userId = $session->get('userId');
        
        // Update role to creator
        $userModel->update($userId, ['role' => 'creator']);
        
        // Create or update profile
        $profileModel->save([
            'user_id'      => $userId,
            'display_name' => $this->request->getPost('display_name'),
            'bio'          => $this->request->getPost('bio'),
        ]);

        // Update session role
        $session->set('role', 'creator');

        return redirect()->to(base_url('creator/dashboard'))->with('success', 'Selamat! Anda sekarang resmi menjadi Kreator.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('creator/login'))->with('success', 'Anda telah berhasil keluar.');
    }
}
