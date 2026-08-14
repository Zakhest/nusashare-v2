<?php

namespace App\Controllers\Creator;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ExploreContentModel;
use App\Models\CreatorProfileModel;

class ContentController extends BaseController
{
    private function guardCreator()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'creator') {
            return redirect()->to(base_url('creator/login'));
        }
        return null;
    }

    private function getOwnedWork(int $id, string $userId): ?array
    {
        $work = (new ExploreContentModel())->find($id);

        if (!$work || (string) $work['creator_id'] !== (string) $userId) {
            return null;
        }

        return $work;
    }

    private function deleteRowsIfTableExists($db, string $table, string $field, int $id): void
    {
        if ($db->tableExists($table)) {
            $db->table($table)->where($field, $id)->delete();
        }
    }

    public function index()
    {
        if ($r = $this->guardCreator()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $userModel          = new UserModel();
        $contentModel       = new ExploreContentModel();
        $creatorProfileModel = new CreatorProfileModel();

        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $works = $contentModel->where('creator_id', $userId)
                              ->orderBy('created_at', 'DESC')
                              ->findAll();

        $data = [
            'title'          => 'Kelola Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'works'          => $works,
            'activePage'     => 'content',
        ];

        return view('creator/content/index', $data);
    }

    public function create()
    {
        if ($r = $this->guardCreator()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $userModel          = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $data = [
            'title'          => 'Tambah Karya Baru - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'activePage'     => 'content',
        ];

        return view('creator/content/create', $data);
    }

    public function store()
    {
        if ($r = $this->guardCreator()) return $r;

        $userId      = session()->get('userId');
        $contentType = $this->request->getPost('content_type');
        $isArtikel   = ($contentType === 'artikel');

        // Validation rules
        $rules = [
            'title'          => 'required|min_length[3]|max_length[255]',
            'description'    => 'required|min_length[10]',
            'content_type'   => 'required|in_list[text,image,pdf,novel,light_novel,comic,artikel]',
            'status'         => 'required|in_list[draft,published]',
            'access_type'    => 'required|in_list[full,chapter]',
            'work_status'    => 'required|in_list[ongoing,ended]',
            'price'          => 'permit_empty|integer|greater_than_equal_to[0]',
            'purchase_price' => 'permit_empty|integer|greater_than_equal_to[0]',
            'cover'          => 'permit_empty|is_image[cover]|max_size[cover,2048]|ext_in[cover,jpg,jpeg,png,webp]',
        ];

        if ($isArtikel) {
            $rules['article_body'] = 'required|min_length[10]';
            $rules['slug']         = 'permit_empty|max_length[255]';
        }

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentModel = new ExploreContentModel();

        $data = [
            'creator_id'     => $userId,
            'title'          => $this->request->getPost('title'),
            'genre'          => $this->request->getPost('genre'),
            'description'    => $this->request->getPost('description'),
            'content_type'   => $contentType,
            'status'         => $this->request->getPost('status'),
            'access_type'    => $isArtikel ? 'full'  : $this->request->getPost('access_type'),
            'work_status'    => $isArtikel ? 'ended' : $this->request->getPost('work_status'),
            'is_paid'        => $isArtikel ? 0 : ($this->request->getPost('is_paid') ? 1 : 0),
            'price'          => $isArtikel ? 0 : (int) $this->request->getPost('price'),
            'purchase_price' => $isArtikel ? 0 : (int) $this->request->getPost('purchase_price'),
            'watermark_text' => $this->request->getPost('watermark_text'),
            'timer_duration' => $isArtikel ? 0 : (int) $this->request->getPost('timer_duration'),
            'is_locked'      => 0,
            'view_count'     => 0,
        ];

        // Handle remote cover upload
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $remoteService = new \App\Services\File\RemoteUploadService();
            $remoteUrl     = $remoteService->upload($cover, 'cover');
            if ($remoteUrl) {
                $data['cover_url'] = str_replace(' ', '%20', $remoteUrl);
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'errors' => ['cover' => 'Gagal mengupload cover ke server remote.']]);
                }
                return redirect()->back()->withInput()->with('errors', ['cover' => 'Gagal mengupload cover ke server remote.']);
            }
        }

        if ($contentModel->insert((object) $data)) {
            $workId = $contentModel->insertID();

            if ($isArtikel) {
                $articleModel = new \App\Models\ArticleModel();
                $inputSlug    = $this->request->getPost('slug');
                $slug         = $articleModel->generateSlug(empty($inputSlug) ? $data['title'] : $inputSlug);

                $articleModel->insert([
                    'work_id' => $workId,
                    'slug'    => $slug,
                    'body'    => $this->request->getPost('article_body'),
                ]);
            } else {
                // Handle multiple gallery images
                $galleryImages = $this->request->getFiles();
                if (isset($galleryImages['gallery_images'])) {
                    $imageModel    = new \App\Models\WorkImageModel();
                    $remoteService = new \App\Services\File\RemoteUploadService();
                    $order         = 1;

                    foreach ($galleryImages['gallery_images'] as $img) {
                        if ($img->isValid() && !$img->hasMoved()) {
                            $remoteUrl = $remoteService->upload($img, 'arts');
                            if ($remoteUrl) {
                                $imageModel->insert((object) [
                                    'work_id'   => $workId,
                                    'file_path' => str_replace(' ', '%20', $remoteUrl),
                                    'order_num' => $order++,
                                ]);
                            }
                        }
                    }
                }
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'redirect' => base_url('creator/content')]);
            }
            return redirect()->to(base_url('creator/content'))->with('message', 'Karya berhasil dibuat!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'errors' => ['db' => 'Gagal menyimpan karya ke database.']]);
        }
        return redirect()->back()->withInput()->with('errors', ['db' => 'Gagal menyimpan karya ke database.']);
    }

    public function edit($id)
    {
        if ($r = $this->guardCreator()) return $r;

        $userId   = session()->get('userId');
        $username = session()->get('username');

        $contentModel        = new ExploreContentModel();
        $userModel           = new UserModel();
        $creatorProfileModel = new CreatorProfileModel();

        $work = $contentModel->find($id);

        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        $user           = $userModel->find($userId);
        $creatorProfile = $creatorProfileModel->find($userId);

        $article = null;
        if ($work['content_type'] === 'artikel') {
            $articleModel = new \App\Models\ArticleModel();
            $article      = $articleModel->findByWorkId($id);
        }

        $data = [
            'title'          => 'Edit Karya - NusaShare',
            'username'       => $username,
            'user'           => $user,
            'creatorProfile' => $creatorProfile,
            'work'           => $work,
            'article'        => $article,
            'activePage'     => 'content',
        ];

        return view('creator/content/edit', $data);
    }

    public function update($id)
    {
        if ($r = $this->guardCreator()) return $r;

        $userId       = session()->get('userId');
        $contentModel = new ExploreContentModel();

        $work = $contentModel->find($id);
        if (!$work || $work['creator_id'] !== $userId) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Akses ditolak.']);
        }

        $contentType = $this->request->getPost('content_type');
        $isArtikel   = ($contentType === 'artikel');

        $rules = [
            'title'          => 'required|min_length[3]|max_length[255]',
            'description'    => 'required|min_length[10]',
            'content_type'   => 'required|in_list[text,image,pdf,novel,light_novel,comic,artikel]',
            'status'         => 'required|in_list[draft,published]',
            'access_type'    => 'required|in_list[full,chapter]',
            'work_status'    => 'required|in_list[ongoing,ended]',
            'price'          => 'permit_empty|integer|greater_than_equal_to[0]',
            'purchase_price' => 'permit_empty|integer|greater_than_equal_to[0]',
            'cover'          => 'permit_empty|is_image[cover]|max_size[cover,3048]|ext_in[cover,jpg,jpeg,png,webp]',
        ];

        if ($isArtikel) {
            $rules['article_body'] = 'required|min_length[10]';
            $rules['slug']         = 'permit_empty|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'title'          => $this->request->getPost('title'),
            'genre'          => $this->request->getPost('genre'),
            'description'    => $this->request->getPost('description'),
            'content_type'   => $contentType,
            'status'         => $this->request->getPost('status'),
            'access_type'    => $isArtikel ? 'full'  : $this->request->getPost('access_type'),
            'work_status'    => $isArtikel ? 'ended' : $this->request->getPost('work_status'),
            'is_paid'        => $isArtikel ? 0 : ($this->request->getPost('is_paid') ? 1 : 0),
            'price'          => $isArtikel ? 0 : (int) $this->request->getPost('price'),
            'purchase_price' => $isArtikel ? 0 : (int) $this->request->getPost('purchase_price'),
            'watermark_text' => $this->request->getPost('watermark_text'),
            'timer_duration' => $isArtikel ? 0 : (int) $this->request->getPost('timer_duration'),
        ];

        // Handle new remote cover upload
        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $remoteService = new \App\Services\File\RemoteUploadService();
            $remoteUrl     = $remoteService->upload($cover, 'cover');

            if ($remoteUrl) {
                $updateData['cover_url'] = str_replace(' ', '%20', $remoteUrl);
            } else {
                return redirect()->back()->withInput()->with('errors', ['cover' => 'Gagal mengupload cover baru ke server remote.']);
            }
        }

        $contentModel->update($id, $updateData);

        if ($isArtikel) {
            $articleModel    = new \App\Models\ArticleModel();
            $existingArticle = $articleModel->findByWorkId($id);

            $inputSlug = $this->request->getPost('slug');
            $slug      = $articleModel->generateSlug(empty($inputSlug) ? $updateData['title'] : $inputSlug, $id);

            if ($existingArticle) {
                $articleModel->update($existingArticle['id'], [
                    'slug' => $slug,
                    'body' => $this->request->getPost('article_body'),
                ]);
            } else {
                $articleModel->insert([
                    'work_id' => $id,
                    'slug'    => $slug,
                    'body'    => $this->request->getPost('article_body'),
                ]);
            }
        } else {
            // Handle additional gallery images (only for non-article)
            $galleryImages = $this->request->getFiles();
            if (isset($galleryImages['gallery_images'])) {
                $imageModel    = new \App\Models\WorkImageModel();
                $remoteService = new \App\Services\File\RemoteUploadService();
                $order         = $imageModel->getNextOrder((int) $id);

                foreach ($galleryImages['gallery_images'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $remoteUrl = $remoteService->upload($img, 'arts');
                        if ($remoteUrl) {
                            $imageModel->insert((object) [
                                'work_id'   => $id,
                                'file_path' => str_replace(' ', '%20', $remoteUrl),
                                'order_num' => $order++,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->to(base_url('creator/content'))
                         ->with('message', 'Karya berhasil diperbarui!');
    }

    public function publish($id)
    {
        if ($r = $this->guardCreator()) return $r;

        $userId = session()->get('userId');
        $work   = $this->getOwnedWork((int) $id, $userId);

        if (!$work) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        (new ExploreContentModel())->update((int) $id, ['status' => 'published']);

        return redirect()->to(base_url('creator/content'))
                         ->with('message', 'Karya berhasil diterbitkan.');
    }

    public function archive($id)
    {
        if ($r = $this->guardCreator()) return $r;

        $userId = session()->get('userId');
        $work   = $this->getOwnedWork((int) $id, $userId);

        if (!$work) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        (new ExploreContentModel())->update((int) $id, ['status' => 'draft']);

        return redirect()->to(base_url('creator/content'))
                         ->with('message', 'Karya berhasil ditarik dan kembali menjadi draft.');
    }

    public function destroy($id)
    {
        if ($r = $this->guardCreator()) return $r;

        $userId       = session()->get('userId');
        $contentModel = new ExploreContentModel();
        $work         = $this->getOwnedWork((int) $id, $userId);

        if (!$work) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['auth' => 'Karya tidak ditemukan atau kamu bukan pemiliknya.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->deleteRowsIfTableExists($db, 'cart_items',      'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'bookmarks',       'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'likes',           'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'comments',        'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'reading_history', 'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'work_images',     'work_id', (int) $id);
        $this->deleteRowsIfTableExists($db, 'articles',        'work_id', (int) $id);

        $chapterIds = $db->table('chapters')
                         ->select('id')
                         ->where('work_id', (int) $id)
                         ->get()
                         ->getResultArray();
        $chapterIds = array_column($chapterIds, 'id');

        if (!empty($chapterIds)) {
            if ($db->tableExists('unlocked_chapters')) {
                $db->table('unlocked_chapters')->whereIn('chapter_id', $chapterIds)->delete();
            }
            $db->table('chapters')->where('work_id', (int) $id)->delete();
        }

        $contentModel->delete((int) $id);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to(base_url('creator/content'))
                             ->with('errors', ['db' => 'Karya gagal dihapus. Coba lagi nanti.']);
        }

        return redirect()->to(base_url('creator/content'))
                         ->with('message', 'Karya berhasil dihapus.');
    }
}
