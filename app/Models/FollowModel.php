<?php

namespace App\Models;

use CodeIgniter\Model;

class FollowModel extends Model
{
    protected $table            = 'follows';
    protected $primaryKey       = 'follower_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['follower_id', 'followed_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get recent works/updates from creators followed by the user
     */
    public function getFollowedUpdates($userId)
    {
        return $this->db->table('works')
            ->select('works.*, COALESCE(user_profiles.display_name, creator_profiles.display_name) as creator_name, COALESCE(user_profiles.profile_image, creator_profiles.profile_image) as profile_image')
            ->join('follows', 'follows.followed_id = works.creator_id')
            ->join('creator_profiles', 'creator_profiles.user_id = works.creator_id', 'left')
            ->join('user_profiles', 'user_profiles.user_id = works.creator_id', 'left')
            ->where('follows.follower_id', $userId)
            ->where('works.status', 'published')
            ->orderBy('works.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();
    }

    /**
     * Get list of creators followed by the user
     */
    public function getFollowedCreators($userId)
    {
        return $this->db->table('follows')
            ->select('follows.*, COALESCE(user_profiles.display_name, creator_profiles.display_name) as display_name, COALESCE(user_profiles.profile_image, creator_profiles.profile_image) as profile_image')
            ->join('creator_profiles', 'creator_profiles.user_id = follows.followed_id', 'left')
            ->join('user_profiles', 'user_profiles.user_id = follows.followed_id', 'left')
            ->where('follower_id', $userId)
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user is following a creator
     */
    public function isFollowing($userId, $creatorId)
    {
        return $this->where('follower_id', $userId)
            ->where('followed_id', $creatorId)
            ->countAllResults() > 0;
    }

    /**
     * Get follower count for a creator
     */
    public function getFollowerCount($creatorId)
    {
        return $this->where('followed_id', $creatorId)->countAllResults();
    }

    /**
     * Get total views/readers for all works of a creator
     */
    public function getTotalReaders($creatorId)
    {
        $result = $this->db->table('works')
            ->selectSum('view_count')
            ->where('creator_id', $creatorId)
            ->get()
            ->getRow();
            
        return (int) ($result->view_count ?? 0);
    }
}
