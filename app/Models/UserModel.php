<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'email',
        'username',
        'password_hash',
        'role',
        'starsoul_value',
        'starsoul_status',
        'engagement',
        'commitment',
        'behavior',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'id'            => 'permit_empty|exact_length[9]|is_unique[users.id]',
        'email'         => 'required|valid_email|is_unique[users.email]',
        'username'      => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
        'password_hash' => 'required|min_length[8]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setupId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function setupId(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUniqueKreatorId();
        }

        return $data;
    }

    private function generateUniqueKreatorId()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $id = '';
            for ($i = 0; $i < 9; $i++) {
                $id .= $characters[rand(0, strlen($characters) - 1)];
            }
            $exists = $this->where('id', $id)->countAllResults() > 0;
        } while ($exists);

        return $id;
    }

    /**
     * Calculate Starsoul value based on formula:
     * (Engagement * 0.5) + (Commitment * 0.3) + (Behavior * 0.2)
     * Capped at 15.5
     */
    public function calculateStarsoul(array $userData): float
    {
        $engagement = (int) ($userData['engagement'] ?? 0);
        $commitment = (int) ($userData['commitment'] ?? 0);
        $behavior = (float) ($userData['behavior'] ?? 0);

        $value = ($engagement * 0.5) + ($commitment * 0.3) + ($behavior * 0.2);
        
        return min(15.5, (float) $value);
    }

    /**
     * Get Starsoul Tier name based on value, only for creators.
     */
    public function getStarsoulTier(float $value, string $role): string
    {
        if ($role !== 'kreator') {
            return 'Soul Explorer';
        }

        if ($value >= 15.0) return 'Imperator Ultimus Stellarum';
        if ($value >= 14.0) return 'King Starlord';
        if ($value >= 13.0) return 'Near King Starlord';
        if ($value >= 11.0) return 'High Starlord';
        if ($value >= 10.0) return 'Starlord';
        if ($value >= 9.0)  return 'High Star';
        if ($value >= 7.0)  return 'Pro Star';
        if ($value >= 4.0)  return 'Rising Star';
        if ($value >= 1.0)  return 'New Stars';

        return 'Soul Explorer';
    }
}
