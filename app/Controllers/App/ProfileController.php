<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;

class ProfileController extends BaseController
{
    /**
     * GET /me/profile
     */
    public function index()
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $userProfileModel = new \App\Models\UserProfileModel();
        $creatorProfileModel = new CreatorProfileModel();

        $user = $userModel->find($userId);
        $userProfile = $userProfileModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        // Merge profile data (user_profile takes precedence for basics if it exists)
        $profile = [
            'display_name'  => $userProfile['display_name'] ?? ($userProfile['display_name'] ?? $user['username']),
            'bio'           => $userProfile['bio'] ?? ($userProfile['bio'] ?? ''),
            'profile_image' => $userProfile['profile_image'] ?? ($userProfile['profile_image'] ?? null),
        ];

        $data = [
            'user'           => $user,
            'username'       => session()->get('username'),
            'profile'        => $profile,
            'creatorProfile' => $creatorProfile, // Keep for role-specific checks
            'isLoggedIn'     => true,
            'activePage'     => 'profile',
            'title'          => 'Profil Saya - NusaShare'
        ];

        return view('me/profile', $data);
    }

    /**
     * POST /me/profile
     */
    public function update()
    {
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('login');
        }

        $userModel = new UserModel();
        $userProfileModel = new \App\Models\UserProfileModel();
        
        // Validation rules
        $rules = [
            'username'     => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$userId}]",
            'display_name' => 'required|min_length[3]|max_length[50]',
            'bio'          => 'permit_empty|max_length[200]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update basic user info
        $userModel->update($userId, [
            'username' => $this->request->getPost('username')
        ]);

        // Handle Profile Image — Cropped base64 takes priority, then raw file upload
        $newFileName = null;
        $targetDir   = 'C:/xampp/htdocs/image-nusashare/profile/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $croppedBase64 = $this->request->getPost('profile_image_cropped');

        if (!empty($croppedBase64)) {
            // Strip data URI header: "data:image/jpeg;base64,..."
            if (preg_match('/^data:image\/(\w+);base64,/', $croppedBase64, $matches)) {
                $imageData   = base64_decode(substr($croppedBase64, strpos($croppedBase64, ',') + 1));
                $newFileName = uniqid('profile_', true) . '.jpg';

                if ($imageData === false || file_put_contents($targetDir . $newFileName, $imageData) === false) {
                    return redirect()->back()->withInput()->with('errors', ['profile_image' => 'Gagal menyimpan foto profil.']);
                }
            }
        } else {
            // Fallback: handle raw file upload (no crop)
            $profileImage = $this->request->getFile('profile_image');
            if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
                $validationRules = [
                    'profile_image' => [
                        'rules' => 'uploaded[profile_image]|is_image[profile_image]|max_size[profile_image,2048]',
                        'label' => 'Foto Profil'
                    ]
                ];
                if ($this->validate($validationRules)) {
                    $newFileName = $profileImage->getRandomName();
                    $profileImage->move($targetDir, $newFileName);
                } else {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
            }
        }


        // Update User Profile
        $profileData = [
            'user_id'      => $userId,
            'display_name' => $this->request->getPost('display_name'),
            'bio'          => $this->request->getPost('bio'),
        ];

        if ($newFileName) {
            $profileData['profile_image'] = $newFileName;
        }

        $userProfileModel->save($profileData);

        // Also update Creator Profile if they are a creator to keep them in sync
        $creatorProfileModel = new CreatorProfileModel();
        if ($creatorProfileModel->find($userId)) {
            $creatorProfileModel->save($profileData);
        }

        // Update session if username changed
        session()->set('username', $this->request->getPost('username'));

        return redirect()->to(base_url('me/profile'))->with('success', 'Profil berhasil diperbarui!');
    }

    /** 
     * GET /user/(:segment)
     */
    public function show($username)
    {
        $userModel = new UserModel();
        $userProfileModel = new \App\Models\UserProfileModel();
        $creatorProfileModel = new CreatorProfileModel();
        $contentModel = new \App\Models\ExploreContentModel();

        $user = $userModel->where('username', $username)->first();

        // Fallback: search by ID if segment is not a username or username not found
        if (!$user) {
            $user = $userModel->find($username);
        }

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User $username tidak ditemukan");
        }

        $userProfile = $userProfileModel->find($user['id']);
        $creatorProfile = $creatorProfileModel->find($user['id']);

        // Merge profile data
        $profile = [
            'display_name'  => $userProfile['display_name'] ?? ($creatorProfile['display_name'] ?? $user['username']),
            'bio'           => $userProfile['bio'] ?? ($creatorProfile['bio'] ?? ''),
            'profile_image' => $userProfile['profile_image'] ?? ($creatorProfile['profile_image'] ?? null),
        ];
        
        // Check if current user follows this creator
        $followModel = new \App\Models\FollowModel();
        $isFollowing = false;
        $userId = session()->get('userId');
        if ($userId) {
            $isFollowing = $followModel->isFollowing($userId, $user['id']);
        }

        // Get works by this user/creator
        $works = $contentModel->where('creator_id', $user['id'])
                              ->whereIn('status', ['published', 'curated', 'museum'])
                              ->findAll();

        $data = [
            'title'          => ($profile['display_name']) . ' - NusaShare',
            'targetUser'     => $user,
            'profile'        => $profile,
            'creatorProfile' => $creatorProfile,
            'works'          => $works,
            'isFollowing'    => $isFollowing,
            'isLoggedIn'     => session()->get('isLoggedIn') ?? false
        ];

        // Fetch current user context if logged in
        $userId = session()->get('userId');
        if ($userId) {
            $data['user'] = $userModel->find($userId);
            $data['username'] = session()->get('username');
        }

        return view('profile/public', $data);
    }
}
