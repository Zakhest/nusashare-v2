<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $exploreModel = new \App\Models\ExploreContentModel();

        // Ambil 8 karya terbaru yang sudah dipublish untuk hero gallery
        $featuredWorks = $exploreModel->getExploreList(8, 0);

        $data = [
            'featuredWorks' => $featuredWorks,
        ];

        return view('index', $data);
    }

    public function terms(): string
    {
        return view('terms', ['title' => 'Syarat dan Ketentuan - NusaShare']);
    }

    public function privacy(): string
    {
        return view('privacy', ['title' => 'Kebijakan Privasi - NusaShare']);
    }

    public function offline(): string
    {
        $this->response->setStatusCode(503);
        return view('offline');
    }
}
