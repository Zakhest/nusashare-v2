<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CreatorProfileModel;
use App\Models\ExploreContentModel;
use App\Models\ChapterModel;

class ChapterController extends BaseController
{
    private function getCreatorContext(): array
    {
        return [
            'userId'   => session()->get('userId'),
            'username' => session()->get('username'),
        ];
    }

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

    private function commonData(array $ctx): array
    {
        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();
        return [
            'user'           => $userModel->find($ctx['userId']),
            'creatorProfile' => $creatorProfileModel->find($ctx['userId']),
            'username'       => $ctx['username'],
            'activePage'     => 'content',
        ];
    }

    // ---------------------------------------------------------------
    // LIST CHAPTERS
    // ---------------------------------------------------------------
    public function index($workId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $chapterModel = new ChapterModel();
        $chapters     = $chapterModel->getByWork((int)$workId);

        return view('creator/chapters/index', array_merge($this->commonData($ctx), [
            'title'    => 'Kelola Bab - ' . $work['title'],
            'work'     => $work,
            'chapters' => $chapters,
        ]));
    }

    // ---------------------------------------------------------------
    // CREATE CHAPTER FORM
    // ---------------------------------------------------------------
    public function create($workId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $chapterModel = new ChapterModel();
        $nextOrder    = $chapterModel->getNextOrder((int)$workId);

        return view('creator/chapters/write', array_merge($this->commonData($ctx), [
            'title'     => 'Tulis Bab Baru',
            'work'      => $work,
            'chapter'   => null,
            'nextOrder' => $nextOrder,
            'mode'      => 'create',
        ]));
    }

    // ---------------------------------------------------------------
    // STORE CHAPTER
    // ---------------------------------------------------------------
    public function store($workId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $contentType = $work['content_type'] ?? 'novel';
        $isComic     = ($contentType === 'comic');
        $isLN        = ($contentType === 'light_novel');
        $hasImages   = ($isComic || $isLN);

        $rules = [
            'title'     => 'required|max_length[200]',
            'status'    => 'required|in_list[draft,published]',
            'is_locked' => 'permit_empty|in_list[0,1]',
            'price'     => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        // Comic/LN chapter body can be built from images; skip body validation for both
        if (!$isComic && !$isLN) {
            $rules['body'] = 'required|min_length[10]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image uploads
        $body = $this->request->getPost('body') ?? '';
        if ($hasImages) {
            $uploadResult  = $this->uploadChapterImages($workId);
            $uploadedPaths = $uploadResult['paths'];
            $failedCount   = $uploadResult['failed'];

            if ($isComic) {
                $body = json_encode($uploadedPaths);
            } elseif (!empty($uploadedPaths)) {
                $body .= '<!--chapter_images:' . json_encode($uploadedPaths) . '-->';
            }

            if ($failedCount > 0) {
                session()->setFlashdata('upload_warning',
                    "{$failedCount} gambar gagal diunggah ke server. Gambar yang berhasil tetap tersimpan."
                );
            }
        }

        $chapterModel = new ChapterModel();
        $chapterModel->insert((object) [
            'work_id'   => (int)$workId,
            'title'     => $this->request->getPost('title'),
            'body'      => $body,
            'order_num' => $chapterModel->getNextOrder((int)$workId),
            'status'    => $this->request->getPost('status'),
            'is_locked' => (int) $this->request->getPost('is_locked'),
            'price'     => (int) $this->request->getPost('price'),
        ]);

        return redirect()->to(base_url("creator/content/{$workId}/chapters"))
                         ->with('message', 'Bab berhasil disimpan!');
    }

    // ---------------------------------------------------------------
    // EDIT CHAPTER FORM
    // ---------------------------------------------------------------
    public function edit($workId, $chapterId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $chapterModel = new ChapterModel();
        $chapter      = $chapterModel->find($chapterId);

        if (!$chapter || (int)$chapter['work_id'] !== (int)$workId) {
            return redirect()->to(base_url("creator/content/{$workId}/chapters"));
        }

        return view('creator/chapters/write', array_merge($this->commonData($ctx), [
            'title'   => 'Edit Bab',
            'work'    => $work,
            'chapter' => $chapter,
            'mode'    => 'edit',
        ]));
    }

    // ---------------------------------------------------------------
    // UPDATE CHAPTER
    // ---------------------------------------------------------------
    public function update($workId, $chapterId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $contentType = $work['content_type'] ?? 'novel';
        $isComic     = ($contentType === 'comic');
        $isLN        = ($contentType === 'light_novel');
        $hasImages   = ($isComic || $isLN);

        $rules = [
            'title'     => 'required|max_length[200]',
            'status'    => 'required|in_list[draft,published]',
            'is_locked' => 'permit_empty|in_list[0,1]',
            'price'     => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        // Comic/LN chapter body can be built from images; skip body validation for both
        if (!$isComic && !$isLN) {
            $rules['body'] = 'required|min_length[10]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image uploads
        $chapterModel = new ChapterModel();
        $existing = $chapterModel->find($chapterId);
        $body = $this->request->getPost('body') ?? ($existing['body'] ?? '');

        if ($hasImages) {
            $uploadResult  = $this->uploadChapterImages($workId);
            $uploadedPaths = $uploadResult['paths'];
            $failedCount   = $uploadResult['failed'];

            if (!empty($uploadedPaths)) {
                if ($isComic) {
                    $existingPaths = [];
                    if ($existing && !empty($existing['body'])) {
                        $decoded = json_decode($existing['body'], true);
                        if (is_array($decoded)) $existingPaths = $decoded;
                    }
                    $body = json_encode(array_merge($existingPaths, $uploadedPaths));
                } else {
                    $body = preg_replace('/<!--chapter_images:.*?-->/', '', $body);
                    $body .= '<!--chapter_images:' . json_encode($uploadedPaths) . '-->';
                }
            } elseif ($isComic) {
                $body = $existing['body'] ?? '[]';
            } else {
                $body = preg_replace('/<!--chapter_images:.*?-->/', '', $body);
                $existingBody = $existing['body'] ?? '';
                if (preg_match('/<!--chapter_images:(.*?)-->/', $existingBody, $m)) {
                    $body .= '<!--chapter_images:' . $m[1] . '-->';
                }
            }

            if ($failedCount > 0) {
                session()->setFlashdata('upload_warning',
                    "{$failedCount} gambar gagal diunggah ke server. Gambar yang berhasil tetap tersimpan."
                );
            }
        }

        $chapterModel->update($chapterId, [
            'title'     => $this->request->getPost('title'),
            'body'      => $body,
            'status'    => $this->request->getPost('status'),
            'is_locked' => (int) $this->request->getPost('is_locked'),
            'price'     => (int) $this->request->getPost('price'),
        ]);

        return redirect()->to(base_url("creator/content/{$workId}/chapters"))
                         ->with('message', 'Bab berhasil diperbarui!');
    }

    // ---------------------------------------------------------------
    // HELPER: Upload chapter images
    // ---------------------------------------------------------------
    private function uploadChapterImages(int $workId): array
    {
        // Gunakan getFileMultiple karena ini input multiple (chapter_images[])
        $files = $this->request->getFileMultiple('chapter_images');
        $paths  = [];
        $failed = 0;

        // Jika tidak ada file sama sekali
        if (empty($files)) {
            return ['paths' => $paths, 'failed' => 0];
        }

        $remoteService = new \App\Services\File\RemoteUploadService();

        foreach ($files as $i => $file) {
            // Pastikan dia adalah object UploadedFile
            if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile) continue;

            // Jika tidak ada file spesifik yang diupload di slot ini (Error = 4)
            if ($file->getError() === UPLOAD_ERR_NO_FILE) continue;

            if (!$file->isValid() || $file->hasMoved()) {
                $failed++;
                continue;
            }

            $remoteUrl = $remoteService->upload($file, 'ilust');
            if ($remoteUrl) {
                $paths[] = str_replace(' ', '%20', $remoteUrl);
            } else {
                $failed++;
                log_message('error', 'Chapter image upload failed for work ' . $workId);
            }
        }

        return ['paths' => $paths, 'failed' => $failed];
    }

    // ---------------------------------------------------------------
    // DELETE CHAPTER
    // ---------------------------------------------------------------
    public function destroy($workId, $chapterId)
    {
        if ($r = $this->guardCreator()) return $r;
        $ctx  = $this->getCreatorContext();
        $work = $this->getWork((int)$workId, $ctx['userId']);
        if (!$work) return redirect()->to(base_url('creator/content'));

        $chapterModel = new ChapterModel();
        $chapter      = $chapterModel->find($chapterId);
        if ($chapter && (int)$chapter['work_id'] === (int)$workId) {
            $chapterModel->delete($chapterId);
        }

        return redirect()->to(base_url("creator/content/{$workId}/chapters"))
                         ->with('message', 'Bab dihapus.');
    }
}
