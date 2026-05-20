<?php

namespace App\Models;

use CodeIgniter\Model;

class CreatorProfileModel extends Model
{
    protected $table            = 'creator_profiles';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 
        'display_name', 
        'bio', 
        'profile_image',
        'timezone', 
        'last_active_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
