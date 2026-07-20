<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\LikeModel;
use App\Models\ExploreContentModel;
use App\Services\NotificationService;

class LikeController extends BaseController
{
    protected $likeModel;

    public function __construct()
    {
        $this->likeModel = new LikeModel();
    }

    /**
     * POST /works/(:num)/like
     */
    public function toggle($workId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda harus login untuk menyukai karya ini.'
            ])->setStatusCode(401);
        }

        $userId = session()->get('userId');
        $result = $this->likeModel->toggleLike($userId, $workId);

        // Kirim notifikasi ke kreator hanya saat like (bukan unlike)
        if ($result['status'] === 'liked') {
            $contentModel = new ExploreContentModel();
            $work = $contentModel->find((int)$workId);

            if ($work && $work['creator_id'] !== $userId) {
                $likerUsername = session()->get('username') ?? $userId;
                (new NotificationService())->notifyLike(
                    $work['creator_id'],
                    $likerUsername,
                    $work['title'],
                    (int)$workId
                );
            }
        }

        return $this->response->setJSON($result);
    }
}
