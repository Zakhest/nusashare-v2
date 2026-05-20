<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\FollowModel;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;

class FollowController extends BaseController
{
    protected $followModel;
    protected $userModel;
    protected $creatorModel;

    public function __construct()
    {
        $this->followModel = new FollowModel();
        $this->userModel = new UserModel();
        $this->creatorModel = new CreatorProfileModel();
    }

    /**
     * GET /me/follows
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userId = session()->get('userId');
        $userProfileModel = new \App\Models\UserProfileModel();
        $user = $this->userModel->find($userId);
        $creatorProfile = $this->creatorModel->find($userId);
        $userProfile = $userProfileModel->find($userId);

        // Merge profile data
        $profile = [
            'display_name'  => $userProfile['display_name'] ?? ($creatorProfile['display_name'] ?? $user['username']),
            'bio'           => $userProfile['bio'] ?? ($creatorProfile['bio'] ?? ''),
            'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
        ];

        $data = [
            'user'           => $user,
            'profile'        => $profile,
            'creatorProfile' => $creatorProfile,
            'updates'        => $this->followModel->getFollowedUpdates($userId),
            'followed'       => $this->followModel->getFollowedCreators($userId),
            'activePage'     => 'follows'
        ];

        return view('dashboard/follows', $data);
    }

    /**
     * POST /follow/(:segment)
     */
    public function store($creatorId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $userId = session()->get('userId');

        if ($userId === $creatorId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak bisa mengikuti diri sendiri.']);
        }

        if ($this->followModel->isFollowing($userId, $creatorId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sudah diikuti.']);
        }

        $this->followModel->insert([
            'follower_id' => $userId,
            'followed_id' => $creatorId
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil mengikuti!']);
    }

    /**
     * GET /follow/(:segment)/stats
     */
    public function stats($creatorId)
    {
        return $this->response->setJSON([
            'status'    => 'success',
            'followers' => $this->followModel->getFollowerCount($creatorId),
            'readers'   => $this->followModel->getTotalReaders($creatorId)
        ]);
    }

    /**
     * POST /follow/(:segment)/remove
     */
    public function destroy($creatorId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login.']);
        }

        $userId = session()->get('userId');

        $this->followModel->where('follower_id', $userId)
            ->where('followed_id', $creatorId)
            ->delete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Batal mengikuti.']);
    }
}
