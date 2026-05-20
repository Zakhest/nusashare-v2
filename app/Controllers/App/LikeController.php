<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\LikeModel;

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

        return $this->response->setJSON($result);
    }
}
