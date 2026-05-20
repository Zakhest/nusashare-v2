<?php
namespace App\Controllers\App;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        $exploreModel = new \App\Models\ExploreContentModel();
        
        // Fetch a random featured work (published/curated/museum)
        $featuredWork = $exploreModel->select('works.*, users.username as creator_name')
            ->join('users', 'users.id = works.creator_id')
            ->whereIn('status', ['published', 'curated', 'museum'])
            ->orderBy('RAND()')
            ->first();

        $data = [
            'title'        => 'Masuk - NusaShare',
            'featuredWork' => $featuredWork
        ];
        
        return view('login', $data);
    }

    public function attempt()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            $sessionData = [
                'userId'     => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ];

            session()->set($sessionData);

            return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . $user['username'] . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
    }

    public function register()
    {
        $data = [
            'title' => 'Daftar - NusaShare',
        ];
        return view('register', $data);
    }

    public function store()
    {
        $userModel = new \App\Models\UserModel();

        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'user',
            'is_active'     => 1,
        ];

        if ($userModel->insert($userData)) {
            return redirect()->to(base_url('login'))->with('success', 'Akun berhasil dibuat! Silakan masuk.');
        } else {
            $errors = $userModel->errors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Gagal membuat akun. Silakan coba lagi.';
            return redirect()->back()->withInput()->with('error', $errorMsg);
        }
    }

    public function forgotPassword()
    {
        return view('forgot_password', ['title' => 'Lupa Kata Sandi - NusaShare']);
    }

    public function attemptForgot()
    {
        $email = $this->request->getPost('email');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar.');
        }

        $token = bin2hex(random_bytes(32));
        $db = \Config\Database::connect();
        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Simulasikan pengiriman email (tampilkan link di flash message untuk testing)
        $resetLink = base_url("reset-password/$token");
        return redirect()->back()->with('success', "Instruksi telah dikirim! (Testing Link: <a href='$resetLink' class='underline'>Klik di sini untuk reset</a>)");
    }

    public function resetPassword($token)
    {
        return view('reset_password', [
            'title' => 'Atur Ulang Sandi - NusaShare',
            'token' => $token
        ]);
    }

    public function attemptReset()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Konfirmasi kata sandi tidak cocok.');
        }

        $db = \Config\Database::connect();
        $resetRequest = $db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$resetRequest) {
            return redirect()->to(base_url('forgot-password'))->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $resetRequest->email)->first();

        if ($user) {
            $userModel->update($user['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            
            // Hapus token setelah digunakan
            $db->table('password_resets')->where('email', $resetRequest->email)->delete();

            return redirect()->to(base_url('login'))->with('success', 'Kata sandi berhasil diperbarui! Silakan masuk.');
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil keluar.');
    }
}