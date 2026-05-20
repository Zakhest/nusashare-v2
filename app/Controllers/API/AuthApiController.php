<?php

namespace App\Controllers\Api;

use App\Models\UserModel;

class AuthApiController extends BaseApiController
{
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            return $this->respondError('Email and password are required.', 400);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            $sessionData = [
                'userId'     => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ];

            session()->set($sessionData);

            return $this->respondSuccess([
                'user' => [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                ]
            ], 'Login successful.');
        }

        return $this->respondError('Invalid email or password.', 401);
    }

    public function register()
    {
        $userModel = new UserModel();

        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return $this->respondError('Validation failed.', 422, $this->validator->getErrors());
        }

        $userData = [
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'user',
            'is_active'     => 1,
        ];

        if ($userModel->insert($userData)) {
            return $this->respondSuccess(null, 'Account created successfully. Please login.', 201);
        } else {
            return $this->respondError('Failed to create account.', 500, $userModel->errors());
        }
    }

    public function logout()
    {
        session()->destroy();
        return $this->respondSuccess(null, 'Logged out successfully.');
    }
}
