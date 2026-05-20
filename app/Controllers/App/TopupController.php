<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreditModel;
use App\Models\CreatorProfileModel;

class TopupController extends BaseController
{
    /**
     * GET /topup
     */
    public function index()
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $creditModel = new CreditModel();
        $creatorProfileModel = new CreatorProfileModel();

        $user = $userModel->find($userId);
        $credit = $creditModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $packages = [
            ['id' => 'p1', 'cc' => 400, 'price' => 4000, 'label' => 'Pocket'],
            ['id' => 'p2', 'cc' => 1000, 'price' => 10000, 'label' => 'Standard'],
            ['id' => 'p3', 'cc' => 5000, 'price' => 50000, 'label' => 'Popular'],
            ['id' => 'p4', 'cc' => 10000, 'price' => 100000, 'label' => 'VIP'],
            ['id' => 'p5', 'cc' => 20000, 'price' => 200000, 'label' => 'Epic'],
            ['id' => 'p6', 'cc' => 100000, 'price' => 1000000, 'label' => 'Mythic'],
        ];

        $data = [
            'user'           => $user,
            'username'       => session()->get('username'),
            'credit'         => $credit,
            'creatorProfile' => $creatorProfile,
            'isLoggedIn'     => true,
            'activePage'     => 'topup',
            'packages'       => $packages,
            'title'          => 'Top Up Cooling Credit - NusaShare'
        ];

        return view('topup', $data);
    }

    /**
     * POST /topup/checkout
     */
    public function checkout()
    {
        $userId = session()->get('userId');
        $packageId = $this->request->getPost('package_id');

        $packages = [
            'p1' => ['cc' => 400, 'label' => 'Pocket'],
            'p2' => ['cc' => 1000, 'label' => 'Standard'],
            'p3' => ['cc' => 5000, 'label' => 'Popular'],
            'p4' => ['cc' => 10000, 'label' => 'VIP'],
            'p5' => ['cc' => 20000, 'label' => 'Epic'],
            'p6' => ['cc' => 100000, 'label' => 'Mythic'],
        ];

        if (!isset($packages[$packageId])) {
            return redirect()->back()->with('error', 'Paket tidak valid.');
        }

        $package = $packages[$packageId];
        $creditModel = new CreditModel();
        
        $credit = $creditModel->find($userId);
        
        if ($credit) {
            $newBalance = $credit['balance'] + $package['cc'];
            $creditModel->update($userId, ['balance' => $newBalance]);
        } else {
            $creditModel->insert([
                'user_id' => $userId,
                'balance' => $package['cc']
            ]);
        }

        return redirect()->to('topup')->with('success', 'Top Up Berhasil! ' . number_format($package['cc']) . ' CC telah ditambahkan ke akun Anda.');
    }
}
