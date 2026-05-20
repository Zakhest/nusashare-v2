<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\CommentModel;
use App\Models\UserModel;

class CommentController extends BaseController
{
    protected $commentModel;

    public function __construct()
    {
        $this->commentModel = new CommentModel();
    }

    /**
     * POST /works/(:num)/comment
     */
    public function store($workId)
    {
        $isAjax = $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if (!session()->get('isLoggedIn')) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'])->setStatusCode(401);
            }
            return redirect()->back()->with('error', 'Anda harus login untuk berkomentar.');
        }

        $userId  = session()->get('userId');
        $content = $this->request->getPost('content');

        if (empty(trim($content))) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Komentar tidak boleh kosong.'])->setStatusCode(422);
            }
            return redirect()->back()->with('error', 'Komentar tidak boleh kosong.');
        }

        $chapterId = $this->request->getPost('chapter_id') ?: null;

        $insertId = $this->commentModel->insert([
            'user_id'    => $userId,
            'work_id'    => $workId,
            'chapter_id' => $chapterId,
            'content'    => $content
        ]);

        if ($isAjax) {
            // Ambil data user untuk dikembalikan ke frontend
            $userModel = new UserModel();
            $user      = $userModel->find($userId);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Komentar berhasil ditambahkan.',
                'comment' => [
                    'id'         => $this->commentModel->getInsertID(),
                    'user_id'    => $userId,
                    'username'   => session()->get('username'),
                    'role'       => $user['role'] ?? 'user',
                    'content'    => $content,
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * POST /comments/(:num)/delete
     */
    public function destroy($commentId)
    {
        $isAjax = $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if (!session()->get('isLoggedIn')) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login.'])->setStatusCode(401);
            }
            return redirect()->back()->with('error', 'Anda harus login.');
        }

        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Komentar tidak ditemukan.'])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Komentar tidak ditemukan.');
        }

        if ($comment['user_id'] !== session()->get('userId')) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki akses.'])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $this->commentModel->delete($commentId);

        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Komentar berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
