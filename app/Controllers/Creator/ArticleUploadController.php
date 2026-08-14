<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Services\File\RemoteUploadService;

class ArticleUploadController extends BaseController
{
    private function guardCreator()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Unauthorized access.'
            ])->setStatusCode(403);
        }
        return null;
    }

    /**
     * POST /creator/artikel/upload-image
     */
    public function uploadImage()
    {
        if ($res = $this->guardCreator()) {
            return $res;
        }

        $validationRule = [
            'image' => [
                'label' => 'Image File',
                'rules' => 'uploaded[image]'
                    . '|is_image[image]'
                    . '|max_size[image,3072]'
                    . '|ext_in[image,png,jpg,jpeg,webp,gif]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => implode(' ', $this->validator->getErrors())
            ]);
        }

        $img = $this->request->getFile('image');
        if ($img->isValid() && !$img->hasMoved()) {
            $remoteService = new RemoteUploadService();
            $url = $remoteService->upload($img, 'arts');

            if ($url) {
                return $this->response->setJSON([
                    'success' => true,
                    'url'     => str_replace(' ', '%20', $url)
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'error'   => 'Failed to upload image to remote server.'
        ]);
    }
}
