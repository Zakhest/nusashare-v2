<?php

namespace App\Controllers;

use League\OAuth2\Client\Provider\Google;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\CreditModel;

class Auth extends BaseController
{
    private $provider;

    public function __construct()
    {
        $this->provider = new Google([
            'clientId'     => env('GOOGLE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET'),
            'redirectUri'  => env('GOOGLE_REDIRECT_URI'),
        ]);
    }

    // 1. Redirect user ke halaman login Google
    public function login()
    {
        $authUrl = $this->provider->getAuthorizationUrl();
        session()->set('oauth2state', $this->provider->getState());

        return redirect()->to($authUrl);
    }

    // 2. Callback dari Google setelah user verifikasi
    public function callback()
    {
        $state = $this->request->getGet('state');
        $code  = $this->request->getGet('code');

        // Validasi state untuk cegah serangan CSRF
        if (empty($state) || ($state !== session()->get('oauth2state'))) {
            session()->remove('oauth2state');
            return redirect()->to('/login')->with('error', 'State tidak valid.');
        }

        try {
            // Ambil Access Token
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            // Ambil data profil user dari Google
            $googleUser = $this->provider->getResourceOwner($token);

            $email  = $googleUser->getEmail();
            $name   = $googleUser->getName();
            $avatar = $googleUser->getAvatar();

            if (empty($email)) {
                return redirect()->to('/login')->with('error', 'Gagal mengambil email dari akun Google Anda.');
            }

            $userModel        = new UserModel();
            $userProfileModel = new UserProfileModel();
            $creditModel      = new CreditModel();

            // Cek apakah user sudah terdaftar berdasarkan email
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                // Generate username unik dari nama / email
                $rawUsername = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $name ?: explode('@', $email)[0]));
                if (strlen($rawUsername) < 3) {
                    $rawUsername = 'user_' . substr(md5(uniqid()), 0, 6);
                }
                $username = substr($rawUsername, 0, 25);
                $baseUsername = $username;
                $counter = 1;
                while ($userModel->where('username', $username)->first()) {
                    $username = substr($baseUsername, 0, 20) . '_' . $counter;
                    $counter++;
                }

                // Data user baru ke database
                $newUserData = [
                    'email'         => $email,
                    'username'      => $username,
                    'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                    'role'          => 'user',
                    'is_active'     => 1,
                ];

                if (!$userModel->insert($newUserData)) {
                    $errors = implode(', ', $userModel->errors());
                    return redirect()->to('/login')->with('error', 'Gagal mendaftarkan user OAuth: ' . $errors);
                }

                // Ambil data user yang baru saja disimpan
                $user = $userModel->where('email', $email)->first();

                // Simpan profil awal di user_profiles
                $userProfileModel->insert([
                    'user_id'       => $user['id'],
                    'display_name'  => $name ?: $username,
                    'profile_image' => $avatar,
                ]);

                // Inisialisasi kredit user
                if (!$creditModel->find($user['id'])) {
                    $creditModel->insert([
                        'user_id' => $user['id'],
                        'balance' => 0,
                    ]);
                }
            } else {
                // User sudah ada, perbarui foto profil atau display_name jika belum ada
                $profile = $userProfileModel->find($user['id']);
                if (!$profile) {
                    $userProfileModel->insert([
                        'user_id'       => $user['id'],
                        'display_name'  => $name ?: $user['username'],
                        'profile_image' => $avatar,
                    ]);
                } else {
                    $updateProfile = [];
                    if (empty($profile['profile_image']) && !empty($avatar)) {
                        $updateProfile['profile_image'] = $avatar;
                    }
                    if (empty($profile['display_name']) && !empty($name)) {
                        $updateProfile['display_name'] = $name;
                    }
                    if (!empty($updateProfile)) {
                        $userProfileModel->update($user['id'], $updateProfile);
                    }
                }

                // Pastikan entry credit ada
                if (!$creditModel->find($user['id'])) {
                    $creditModel->insert([
                        'user_id' => $user['id'],
                        'balance' => 0,
                    ]);
                }
            }

            // Simpan ke session login aplikasi
            $sessionData = [
                'userId'     => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ];

            session()->set($sessionData);

            // Redirect sesuai histori atau role
            $redirectUrl = session()->get('redirect_url');
            if ($redirectUrl) {
                session()->remove('redirect_url');
                return redirect()->to($redirectUrl)->with('success', 'Selamat datang kembali, ' . $user['username'] . '!');
            }

            switch ($user['role']) {
                case 'admin':
                    return redirect()->to(base_url('alpha-admin'))->with('success', 'Halo Admin, Panel Kontrol NusaShare Siap Dioperasikan!');
                case 'creator':
                    return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, Kreator ' . $user['username'] . '!');
                case 'editor':
                    return redirect()->to(base_url('editor/dashboard'))->with('success', 'Selamat bekerja, Editor ' . $user['username'] . '!');
                default:
                    return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . $user['username'] . '!');
            }

        } catch (\Exception $e) {
            return redirect()->to('/login')->with('error', 'Gagal login: ' . $e->getMessage());
        }
    }
}