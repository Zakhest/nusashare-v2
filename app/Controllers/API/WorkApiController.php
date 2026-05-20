<?php

namespace App\Controllers\Api;

use App\Models\ExploreContentModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\BookmarkModel;

class WorkApiController extends BaseApiController
{
    public function index()
    {
        $contentModel = new ExploreContentModel();
        $works = $contentModel->select('works.*, users.username as creator_name')
                              ->join('users', 'users.id = works.creator_id')
                              ->whereIn('status', ['published', 'curated', 'museum'])
                              ->orderBy('created_at', 'DESC')
                              ->findAll();

        return $this->respondSuccess($works, 'Works retrieved successfully.');
    }

    public function show($id)
    {
        $contentModel = new ExploreContentModel();
        $work = $contentModel->select('works.*, users.username as creator_name')
                             ->join('users', 'users.id = works.creator_id')
                             ->find($id);

        if (!$work) {
            return $this->respondError('Work not found.', 404);
        }

        return $this->respondSuccess($work, 'Work details retrieved successfully.');
    }

    public function toggleLike($workId)
    {
        $userId = session()->get('userId');
        $likeModel = new LikeModel();

        $existing = $likeModel->where('user_id', $userId)
                              ->where('work_id', $workId)
                              ->first();

        if ($existing) {
            $likeModel->delete($existing['id']);
            return $this->respondSuccess(['liked' => false], 'Unliked successfully.');
        } else {
            $likeModel->insert([
                'user_id' => $userId,
                'work_id' => $workId
            ]);
            return $this->respondSuccess(['liked' => true], 'Liked successfully.');
        }
    }

    public function postComment($workId)
    {
        $userId = session()->get('userId');
        $commentModel = new CommentModel();

        $content = $this->request->getPost('content');
        if (!$content) {
            return $this->respondError('Comment content is required.', 400);
        }

        $commentData = [
            'user_id'    => $userId,
            'work_id'    => $workId,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($commentModel->insert($commentData)) {
            return $this->respondSuccess($commentData, 'Comment posted successfully.', 201);
        }

        return $this->respondError('Failed to post comment.', 500);
    }

    public function toggleBookmark($workId)
    {
        $userId = session()->get('userId');
        $bookmarkModel = new BookmarkModel();

        $existing = $bookmarkModel->where('user_id', $userId)
                                  ->where('work_id', $workId)
                                  ->first();

        if ($existing) {
            $bookmarkModel->delete($existing['id']);
            return $this->respondSuccess(['bookmarked' => false], 'Bookmark removed.');
        } else {
            $bookmarkModel->insert([
                'user_id' => $userId,
                'work_id' => $workId
            ]);
            return $this->respondSuccess(['bookmarked' => true], 'Bookmarked successfully.');
        }
    }
}
