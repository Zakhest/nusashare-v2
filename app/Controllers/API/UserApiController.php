<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\CreatorProfileModel;

class UserApiController extends BaseApiController
{
    public function getProfile()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->respondError('User not found.', 404);
        }

        unset($user['password_hash']);

        return $this->respondSuccess([
            'user' => $user
        ], 'Profile retrieved successfully.');
    }

    public function updateProfile()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();

        $rules = [
            'username'     => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$userId}]",
        ];

        if (!$this->validate($rules)) {
            return $this->respondError('Validation failed.', 422, $this->validator->getErrors());
        }

        $updateData = [
            'username' => $this->request->getPost('username')
        ];

        if ($userModel->update($userId, $updateData)) {
            session()->set('username', $updateData['username']);
            return $this->respondSuccess($updateData, 'Profile updated successfully.');
        }

        return $this->respondError('Failed to update profile.', 500);
    }
}
