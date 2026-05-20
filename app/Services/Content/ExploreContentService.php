<?php

namespace App\Services\Content;

use App\Models\ExploreContentModel;

class ExploreContentService
{
    protected ExploreContentModel $contentModel;

    public function __construct()
    {
        $this->contentModel = new ExploreContentModel();
    }

    /**
     * Ambil list konten untuk halaman explore dengan filter kolom yang sesuai database
     */
    public function getExploreList(string $sort = 'latest', ?int $userId = null, string $type = ''): array
    {
        $builder = $this->contentModel->builder();
        
        // Setup awal: Select kolom sesuai struktur tabel works
        $builder->select([
            'works.id',
            'works.creator_id',
            'works.title',
            'works.description',
            'works.content_type',
            'works.status',
            'works.cover_url',
            'works.view_count',
            'works.created_at',
            'users.username as creator_username',
            'COALESCE(user_profiles.display_name, creator_profiles.display_name) as creator_name'
        ])
        ->join('users', 'users.id = works.creator_id')
        ->join('creator_profiles', 'creator_profiles.user_id = works.creator_id', 'left')
        ->join('user_profiles', 'user_profiles.user_id = works.creator_id', 'left')
        ->whereIn('works.status', ['published', 'curated', 'museum']);

        // Filter berdasarkan tipe konten
        $allowedTypes = ['novel', 'light_novel', 'comic', 'text'];
        if (!empty($type) && in_array($type, $allowedTypes)) {
            $builder->where('works.content_type', $type);
        }

        // Logika Sorting
        if ($sort === 'popular') {
            $builder->orderBy('works.view_count', 'DESC');
        } elseif ($sort === 'trending') {
            // Trending = view tinggi dalam 7 hari terakhir
            $builder->where('works.created_at >=', date('Y-m-d', strtotime('-7 days')))
                    ->orderBy('works.view_count', 'DESC');
        } else {
            $builder->orderBy('works.created_at', 'DESC');
        }

        $works = $builder
            ->limit(24)
            ->get()
            ->getResultArray();

        // Check bookmarks if userId is provided
        if ($userId && !empty($works)) {
            $workIds = array_column($works, 'id');
            $bookmarkModel = new \App\Models\BookmarkModel();
            $bookmarks = $bookmarkModel->where('user_id', $userId)
                                      ->whereIn('work_id', $workIds)
                                      ->findAll();
            
            $bookmarkedIds = array_column($bookmarks, 'work_id');
            
            foreach ($works as &$work) {
                $work['is_bookmarked'] = in_array($work['id'], $bookmarkedIds);
            }
        } else {
            foreach ($works as &$work) {
                $work['is_bookmarked'] = false;
            }
        }

        return [
            'works' => $works,
            'sort'  => $sort,
            'type'  => $type,
        ];
    }

    /**
     * Search works by title or description
     */
    public function searchWorks(string $query): array
    {
        $builder = $this->contentModel->builder();
        return $builder->select([
            'works.id',
            'works.creator_id',
            'works.title',
            'works.description',
            'works.content_type',
            'works.status',
            'works.cover_url',
            'works.view_count',
            'works.created_at',
            'users.username as creator_username',
            'COALESCE(user_profiles.display_name, creator_profiles.display_name) as creator_name'
        ])
        ->join('users', 'users.id = works.creator_id')
        ->join('creator_profiles', 'creator_profiles.user_id = works.creator_id', 'left')
        ->join('user_profiles', 'user_profiles.user_id = works.creator_id', 'left')
        ->whereIn('works.status', ['published', 'curated', 'museum'])
        ->groupStart()
            ->like('works.title', $query)
            ->orLike('works.description', $query)
        ->groupEnd()
        ->limit(10)
        ->get()
        ->getResultArray();
    }

    /**
     * Search users (including creators) by username or display name
     */
    public function searchUsers(string $query): array
    {
        $userModel = new \App\Models\UserModel();
        $builder = $userModel->builder();
        
        return $builder->select([
            'users.id',
            'users.username',
            'users.role',
            'COALESCE(user_profiles.display_name, creator_profiles.display_name) as display_name',
            'COALESCE(user_profiles.bio, creator_profiles.bio) as bio',
            'COALESCE(user_profiles.profile_image, creator_profiles.profile_image) as profile_image'
        ])
        ->join('creator_profiles', 'creator_profiles.user_id = users.id', 'left')
        ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
        ->groupStart()
            ->like('users.username', $query)
            ->orLike('creator_profiles.display_name', $query)
            ->orLike('user_profiles.display_name', $query)
        ->groupEnd()
        ->limit(10)
        ->get()
        ->getResultArray();
    }
}