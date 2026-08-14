<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\BookmarkModel;
use App\Models\ExploreContentModel;

class ArticleController extends BaseController
{
    private const VIEW_THRESHOLD_SECONDS = 120;

    /**
     * GET /artikel/(:segment)
     * Tampilkan halaman artikel publik.
     */
    public function show(string $slug)
    {
        helper('article');

        $articleModel = new ArticleModel();
        $article      = $articleModel->findBySlug($slug);

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }

        // Render body menjadi HTML
        $article['body_html'] = render_article_body($article['body']);

        // View threshold (hitungan tayangan setelah 2 menit)
        $this->startViewThreshold((int) $article['work_id']);

        $likeModel    = new LikeModel();
        $commentModel = new CommentModel();

        $data = [
            'article'    => $article,
            'isLoggedIn' => session()->get('isLoggedIn') ?? false,
            'likeCount'  => $likeModel->getCount($article['work_id']),
            'hasLiked'   => false,
            'hasBookmarked' => false,
            'comments'   => $commentModel->getByWork($article['work_id']),
        ];

        // Simpan URL untuk redirect setelah login
        if (!session()->get('isLoggedIn')) {
            session()->set('redirect_url', current_url());
        }

        $userId = session()->get('userId');
        if ($userId) {
            $userModel           = new UserModel();
            $creatorProfileModel = new CreatorProfileModel();
            $bookmarkModel       = new BookmarkModel();

            $data['user']           = $userModel->find($userId);
            $data['username']       = session()->get('username');
            $data['creatorProfile'] = $creatorProfileModel->find($userId);
            $data['hasLiked']       = $likeModel->hasLiked($userId, $article['work_id']);
            $data['hasBookmarked']  = $bookmarkModel
                ->where('user_id', $userId)
                ->where('work_id', $article['work_id'])
                ->countAllResults() > 0;
        }

        return view('artikel/show', $data);
    }

    /**
     * POST /works/(:num)/view  — reused via WorkController alias
     * Dicatat view count setelah 2 menit.
     */
    private function startViewThreshold(int $workId): void
    {
        $session    = session();
        $startedKey = 'view_started_work_' . $workId;
        $countedKey = 'view_counted_work_' . $workId;

        if ($session->get($countedKey) || $session->get($startedKey)) {
            return;
        }

        $session->set($startedKey, time());
    }
}
