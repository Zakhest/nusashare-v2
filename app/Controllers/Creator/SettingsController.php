<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;

class SettingsController extends BaseController
{
    public function index()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');
        $username = session()->get('username');

        $userModel = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Get user and creator data
        $user = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $data = [
            'title'          => 'Pengaturan Profil - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'activePage'     => 'settings'
        ];

        return view('creator/settings/index', $data);
    }

    public function update()
    {
        // Check if logged in as creator
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }

        $userId = session()->get('userId');
        $creatorProfileModel = new CreatorProfileModel();

        $data = [
            'display_name' => $this->request->getPost('display_name'),
            'bio'          => $this->request->getPost('bio'),
        ];

        // Basic validation
        if (empty($data['display_name'])) {
            return redirect()->back()->with('error', 'Nama tampilan tidak boleh kosong.');
        }

        if ($creatorProfileModel->update($userId, $data)) {
            return redirect()->to(base_url('creator/settings'))->with('success', 'Profil berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui profil.');
        }
    }
}
