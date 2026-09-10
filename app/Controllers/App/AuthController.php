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
                'role'       => $user['role'], // Mengambil ENUM: user, creator, editor, admin
                'isLoggedIn' => true,
            ];

            session()->set($sessionData);

            // Jika ada URL yang dituju sebelum login, kembalikan ke sana
            $redirectUrl = session()->getFlashdata('redirect_url') ?? session()->get('redirect_url');
            if ($redirectUrl) {
                session()->remove('redirect_url');
                // Hanya izinkan redirect ke URL internal (hindari open redirect)
                $baseHost = parse_url(base_url(), PHP_URL_HOST);
                $targetHost = parse_url($redirectUrl, PHP_URL_HOST);
                if ($targetHost === $baseHost || $targetHost === null) {
                    $successMsg = 'Selamat datang kembali, ' . $user['username'] . '!';
                    return redirect()->to($redirectUrl)->with('success', $successMsg);
                }
            }

            // TENTUKAN GERBANG MASUK BERDASARKAN ROLE DI DATABASE
            switch ($user['role']) {
                case 'admin':
                    return redirect()->to(base_url('alpha-admin'))->with('success', 'Halo Admin, Panel Kontrol NusaShare Siap Dioperasikan!');
                
                case 'creator':
                    return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, Kreator ' . $user['username'] . '!');
                
                case 'editor':
                    return redirect()->to(base_url('editor/dashboard'))->with('success', 'Selamat bekerja, Editor ' . $user['username'] . '!');
                
                default: // 'user'
                    return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . $user['username'] . '!');
            }
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
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan alamat email yang valid.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar pada sistem kami.');
        }

        $token = bin2hex(random_bytes(32));
        $db = \Config\Database::connect();
        
        // Bersihkan token lama untuk email ini jika ada
        $db->table('password_resets')->where('email', $email)->delete();

        // Simpan token baru
        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $resetLink = base_url("reset-password/$token");

        // Render template email
        $emailBody = view('emails/forgot_password', [
            'username'  => $user['username'] ?? 'Pengguna',
            'email'     => $email,
            'resetLink' => $resetLink
        ]);

        // Kirim email via Service Email CodeIgniter
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Atur Ulang Kata Sandi - NusaShare');
        $emailService->setMessage($emailBody);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Tautan untuk mengatur ulang kata sandi telah dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam Anda.');
        } else {
            // Log error jika pengiriman gagal
            log_message('error', 'Email reset password gagal terkirim: ' . $emailService->printDebugger(['headers']));
            
            // Berikan notifikasi jika gagal terhubung ke SMTP
            return redirect()->back()->with('error', 'Gagal mengirim email reset kata sandi. Pastikan konfigurasi email/SMTP sudah benar.');
        }
    }

    public function resetPassword($token)
    {
        $db = \Config\Database::connect();
        $resetRequest = $db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$resetRequest) {
            return redirect()->to(base_url('forgot-password'))->with('error', 'Tautan reset kata sandi tidak valid atau telah digunakan.');
        }

        // Cek kedaluwarsa token (1 jam = 3600 detik)
        $createdAt = strtotime($resetRequest->created_at);
        if ((time() - $createdAt) > 3600) {
            $db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to(base_url('forgot-password'))->with('error', 'Tautan reset kata sandi telah kedaluwarsa (berlaku 1 jam). Silakan minta tautan baru.');
        }

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

        $rules = [
            'token'            => 'required',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'password' => [
                'min_length' => 'Kata sandi minimal harus 8 karakter.'
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi kata sandi tidak cocok.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $resetRequest = $db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$resetRequest) {
            return redirect()->to(base_url('forgot-password'))->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        // Cek masa berlaku token (1 jam)
        $createdAt = strtotime($resetRequest->created_at);
        if ((time() - $createdAt) > 3600) {
            $db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to(base_url('forgot-password'))->with('error', 'Token telah kedaluwarsa. Silakan ajukan ulang.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $resetRequest->email)->first();

        if ($user) {
            $userModel->update($user['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            
            // Hapus token setelah berhasil digunakan
            $db->table('password_resets')->where('email', $resetRequest->email)->delete();

            return redirect()->to(base_url('login'))->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
        }

        return redirect()->back()->with('error', 'Akun tidak ditemukan. Silakan coba lagi.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil keluar.');
    }
}