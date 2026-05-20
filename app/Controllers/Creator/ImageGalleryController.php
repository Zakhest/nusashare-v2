<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\ExploreContentModel;
use App\Models\WorkImageModel;

class ImageGalleryController extends BaseController
{
    private function guardCreator()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }
        return null;
    }

    private function getWork(int $workId, string $userId)
    {
        $work = (new ExploreContentModel())->find($workId);
        if (!$work || $work['creator_id'] !== $userId) {
            return null;
        }
        return $work;
    }

    private function commonData(): array
    {
        $userId = session()->get('userId');
        return [
            'user'           => (new UserModel())->find($userId),
            'creatorProfile' => (new CreatorProfileModel())->find($userId),
            'username'       => session()->get('username'),
            'activePage'     => 'content',
        ];
    }

    // ---------------------------------------------------------------
    // GALLERY INDEX
    // ---------------------------------------------------------------
    public function index($workId)
    {
        if ($r = $this->guardCreator()) return $r;
        $userId = session()->get('userId');
        $work   = $this->getWork((int)$workId, $userId);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $imageModel = new WorkImageModel();
        $images     = $imageModel->getByWork((int)$workId);

        return view('creator/images/index', array_merge($this->commonData(), [
            'title'  => 'Kelola Gambar - ' . $work['title'],
            'work'   => $work,
            'images' => $images,
        ]));
    }

    // ---------------------------------------------------------------
    // UPLOAD IMAGES
    // ---------------------------------------------------------------
    public function upload($workId)
    {
        if ($r = $this->guardCreator()) return $r;
        $userId = session()->get('userId');
        $work   = $this->getWork((int)$workId, $userId);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $files = $this->request->getFiles();
        if (empty($files['images'])) {
            return redirect()->back()->with('errors', ['files' => 'Pilih minimal satu gambar.']);
        }

        $uploadPath = ROOTPATH . 'public/uploads/works/' . $workId;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $imageModel = new WorkImageModel();
        $remoteService = new \App\Services\File\RemoteUploadService();
        $order      = $imageModel->getNextOrder((int)$workId);
        $uploaded   = 0;
        $failed     = 0;

        // getFiles() returns array of UploadedFile for multi-file input
        foreach ($files['images'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $ext    = $file->getClientExtension();
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                if (!in_array(strtolower($ext), $allowedExts)) continue;

                $remoteUrl = $remoteService->upload($file, 'arts');

                if ($remoteUrl) {
                    $imageModel->insert((object) [
                        'work_id'   => (int)$workId,
                        'file_path' => str_replace(' ', '%20', $remoteUrl),
                        'order_num' => $order++,
                    ]);
                    $uploaded++;
                } else {
                    $failed++;
                }
            }
        }

        $msg = $uploaded > 0 ? "{$uploaded} gambar berhasil diupload ke remote server!" : "Tidak ada gambar yang berhasil diupload.";
        if ($failed > 0) $msg .= " ({$failed} gagal)";
        return redirect()->to(base_url("creator/content/{$workId}/images"))->with('message', $msg);
    }

    // ---------------------------------------------------------------
    // DELETE IMAGE
    // ---------------------------------------------------------------
    public function destroy($workId, $imageId)
    {
        if ($r = $this->guardCreator()) return $r;
        $userId = session()->get('userId');
        $work   = $this->getWork((int)$workId, $userId);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $imageModel = new WorkImageModel();
        $image      = $imageModel->find($imageId);

        if ($image && (int)$image['work_id'] === (int)$workId) {
            // Delete file from disk if it's local
            $filePath = $image['file_path'];
            if (!filter_var($filePath, FILTER_VALIDATE_URL)) {
                $fullPath = ROOTPATH . 'public/' . $filePath;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $imageModel->delete($imageId);
        }

        return redirect()->to(base_url("creator/content/{$workId}/images"))
                         ->with('message', 'Gambar dihapus.');
    }
}
