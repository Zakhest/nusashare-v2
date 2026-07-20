<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\BookmarkModel;
use App\Models\ExploreContentModel;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\CartModel;

class BookmarkController extends BaseController
{
    /**
     * GET /me/bookmarks
     */
    public function index()
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('login');
        }

        $bookmarkModel = new BookmarkModel();
        $contentModel = new ExploreContentModel();
        $userModel = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        // Get bookmarked IDs
        $bookmarks = $bookmarkModel->where('user_id', $userId)->findAll();
        $workIds = array_column($bookmarks, 'work_id');

        $works = [];
        if (!empty($workIds)) {
            $works = $contentModel->builder()
                ->select([
                    'works.id',
                    'works.creator_id',
                    'works.title',
                    'works.description',
                    'works.content_type',
                    'works.status',
                    'works.cover_url',
                    'works.view_count',
                    'works.is_paid',
                    'works.price',
                    'works.purchase_price',
                    'works.work_status',
                    'works.created_at',
                    'users.username as creator_name'
                ])
                ->join('users', 'users.id = works.creator_id')
                ->whereIn('works.id', $workIds)
                ->orderBy('works.created_at', 'DESC')
                ->get()
                ->getResultArray();
        }

        $cartModel = new CartModel();

        $data = [
            'works'          => $works,
            'user'           => $userModel->find($userId),
            'username'       => session()->get('username'),
            'creatorProfile' => $creatorProfileModel->find($userId),
            'cartWorkIds'    => $cartModel->getWorkIds((string)$userId),
            'isLoggedIn'     => true,
            'activePage'     => 'bookmarks'
        ];

        return view('me/bookmarks', $data);
    }

    /**
     * POST /bookmark/(:num)
     */
    public function store($workId)
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan masuk terlebih dahulu.']);
        }

        $bookmarkModel = new BookmarkModel();
        
        // Check if already bookmarked
        $exists = $bookmarkModel->where('user_id', $userId)->where('work_id', $workId)->countAllResults() > 0;
        if ($exists) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Sudah ada di koleksi.']);
        }

        $bookmarkModel->insert([
            'user_id' => $userId,
            'work_id' => $workId
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil ditambahkan ke koleksi.']);
    }

    /**
     * POST /bookmark/(:num)/remove
     */
    public function destroy($workId)
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan masuk terlebih dahulu.']);
        }

        $bookmarkModel = new BookmarkModel();
        
        $bookmarkModel->where('user_id', $userId)->where('work_id', $workId)->delete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil dihapus dari koleksi.']);
    }
}
