<?php

namespace App\Controllers\App;

use App\Controllers\BaseController;
use App\Models\ExploreContentModel;
use App\Models\WorkImageModel;

class ImageController extends BaseController
{
    private const IMAGE_BROWSER_CACHE_SECONDS = 604800; // 7 days

    /**
     * Proxy for Work Cover
     */
    public function cover($workId)
    {
        if (!$this->isAuthorized((int)$workId)) {
            return $this->response->setStatusCode(403)->setBody('Direct access is allowed.');
        }

        $model = new ExploreContentModel();
        $work = $model->find($workId);

        if (!$work || empty($work['cover_url'])) {
            return $this->response->setStatusCode(404);
        }

        return $this->streamImage($work['cover_url']);
    }

    /**
     * Proxy for Gallery Image
     */
    public function gallery($imageId)
{
    $model = new WorkImageModel();
    $image = $model->find($imageId);

    if (!$image || empty($image['file_path'])) {
        return $this->response->setStatusCode(404);
    }

    // Ambil data work untuk mendapatkan watermark_text dari database
    $workModel = new \App\Models\ExploreContentModel();
    $work = $workModel->find($image['work_id']);

    // Check authorization (Logika Sec-Fetch-Dest yang sudah sukses tadi)
    if (!$this->isAuthorized((int)$image['work_id'])) {
        return $this->response->setStatusCode(403)->setBody('Direct access not allowed.');
    }

    // Ambil teks dari DB, jika kosong gunakan username session atau default
    $watermarkText = $work['watermark_text'] ?? session()->get('username') ?? 'NusaShare';

    // Cek apakah konten terkunci (hanya blur jika ini konten berbayar, user belum bayar, dan user BUKAN pembuat konten)
    $isLocked = false;
    if ($work['is_paid']) {
        $userId = session()->get('userId');
        $isCreator = $userId && (int)$work['creator_id'] === (int)$userId;
        $unlockedUntil = session()->get('unlocked_' . $work['id']);
        $hasUnlocked = $unlockedUntil && time() <= $unlockedUntil;
        
        if (!$isCreator && !$hasUnlocked) {
            $isLocked = true;
        }
    }

    // Kirim path DAN teks watermark ke streamImage. Tambahkan status locked.
    return $this->streamImage($image['file_path'], $watermarkText, $isLocked);
}

    /**
     * Serve chapter illustration images (LN & Comic) from writable/uploads/
     * Route: GET image/chapter/(:num)/(:segment)
     */
    public function chapterImage($workId, $filename)
    {
        // Sanitize filename to prevent path traversal
        $filename = basename($filename);
        if (!preg_match('/^[\w\-\.]+$/i', $filename)) {
            return $this->response->setStatusCode(400)->setBody('Invalid filename.');
        }

        $filePath = WRITEPATH . 'uploads/chapters/' . (int)$workId . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404);
        }

        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        $content  = file_get_contents($filePath);

        $etag = $this->etagFromFile($filePath);
        $lastModified = filemtime($filePath) ?: time();

        if ($this->clientHasFreshImage($etag, $lastModified)) {
            return $this->imageResponse($mimeType, '', $etag, $lastModified)->setStatusCode(304);
        }

        return $this->imageResponse($mimeType, $content, $etag, $lastModified);
    }

    /**
     * Proxy for Remote Images (from CDN) — hides the real external link.
     * Route: GET image/proxy-remote
     */
    public function proxyRemote()
    {
        // Prevent direct access via new tab / direct URL
        $fetchDest = $this->request->getHeaderLine('Sec-Fetch-Dest');
        $referer   = $this->request->getServer('HTTP_REFERER');
        $baseUrl   = base_url();

        if ($fetchDest === 'document' || ($fetchDest === '' && (empty($referer) || strpos($referer, $baseUrl) !== 0))) {
            return $this->response
                ->setStatusCode(403)
                ->setHeader('Content-Type', 'text/html')
                ->setBody('<script>alert("Akses langsung ke gambar tidak diizinkan."); window.location.href="' . $baseUrl . '";</script>');
        }

        $q = $this->request->getGet('q');
        if (!$q) return $this->response->setStatusCode(400);

        // Decode Base64 encoded URL
        $url = base64_decode($q);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->response->setStatusCode(400)->setBody('Invalid URL.');
        }

        // Only allow proxying specific CDN to prevent SSRF vulnerabilities
        if (strpos($url, 'z-enterprise.my.id') === false) {
             return $this->response->setStatusCode(403)->setBody('Forbidden source.');
        }

        try {
            $client = \Config\Services::curlrequest();
            $resp   = $client->get($url, ['http_errors' => false, 'verify' => false]);
            
            if ($resp->getStatusCode() !== 200) {
                return $this->response->setStatusCode(404);
            }

            $content = $resp->getBody();
            $etag = '"' . md5($url . $content) . '"';
            $lastModified = time();

            if ($this->clientHasFreshImage($etag, $lastModified)) {
                return $this->imageResponse($resp->getHeaderLine('Content-Type'), '', $etag, $lastModified, 'public')->setStatusCode(304);
            }

            return $this->imageResponse($resp->getHeaderLine('Content-Type'), $content, $etag, $lastModified, 'public');
        } catch (\Exception $e) {
            return $this->response->setStatusCode(404);
        }
    }

    /**
     * Proxy for Profile Image — hides real file path from browser.
     * Route: GET image/profile/(:segment)
     */
    public function profile($filename)
    {
        // Decode base64 jika filename merupakan URL terenkode
        $decodedUrl = base64_decode($filename, true);
        if ($decodedUrl && (str_starts_with($decodedUrl, 'http://') || str_starts_with($decodedUrl, 'https://'))) {
            try {
                $client = \Config\Services::curlrequest();
                $resp   = $client->get($decodedUrl, ['http_errors' => false, 'verify' => false, 'timeout' => 10]);
                if ($resp->getStatusCode() === 200) {
                    return $this->response
                        ->setHeader('Content-Type', $resp->getHeaderLine('Content-Type') ?: 'image/jpeg')
                        ->setHeader('Cache-Control', 'public, max-age=604800')
                        ->setBody($resp->getBody());
                }
            } catch (\Exception $e) {
                return $this->response->setStatusCode(404);
            }
        }

        // Sanitize: only allow safe filename characters (no path traversal)
        if (!preg_match('/^[\w\-\.]+$/i', $filename)) {
            return $this->response->setStatusCode(400)->setBody('Invalid filename.');
        }

        $filePath = 'C:/xampp/htdocs/image-nusashare/profile/' . $filename;

        if (!file_exists($filePath)) {
            $altPaths = [
                WRITEPATH . 'uploads/profile/' . $filename,
                FCPATH . 'uploads/profile/' . $filename,
                FCPATH . 'assets/img/' . $filename,
            ];
            $found = false;
            foreach ($altPaths as $alt) {
                if (file_exists($alt)) {
                    $filePath = $alt;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return $this->response->setStatusCode(404);
            }
        }

        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        $content  = file_get_contents($filePath);

        $etag = $this->etagFromFile($filePath);
        $lastModified = filemtime($filePath) ?: time();

        if ($this->clientHasFreshImage($etag, $lastModified)) {
            return $this->imageResponse($mimeType, '', $etag, $lastModified)->setStatusCode(304);
        }

        return $this->imageResponse($mimeType, $content, $etag, $lastModified);
    }

    /**
     * Check if request is authorized
     */
   private function isAuthorized($workId)
{
    // Cek Fetch Metadata (Hanya didukung browser modern: Chrome, Edge, Firefox, Safari)
    $fetchDest = $this->request->getHeaderLine('Sec-Fetch-Dest');
    
    // Jika tujuannya adalah 'document', berarti user buka di tab baru / direct URL
    if ($fetchDest === 'document') {
        return false;
    }

    // Browser mobile dan beberapa in-app webview kadang tidak mengirim Referer,
    // tetapi tetap mengirim Fetch Metadata untuk request gambar. Izinkan kasus ini
    // supaya cover/galeri tidak gagal load di mobile, sambil tetap memblok direct open.
    if ($fetchDest === 'image') {
        return true;
    }

    $referer = $this->request->getServer('HTTP_REFERER');
    $baseUrl = base_url();

    // Logika Session tetap ada untuk pemilik (opsional)
    $userId = session()->get('userId');
    if ($userId) {
        $model = new ExploreContentModel();
        $work = $model->find($workId);
        if ($work && (int)$work['creator_id'] === (int)$userId) {
            return true;
        }
    }

    // Cek Referer sebagai cadangan (fallback)
    if (empty($referer) || strpos($referer, $baseUrl) !== 0) {
        return false;
    }

    return true;
}

    /**
     * Helper to stream image from local or remote
     */
   private function streamImage($path, $watermarkText = null, $isLocked = false)
{
    $content = null;
    $mimeType = 'image/jpeg';
    
    // --- CACHE CHECK ---
    $cacheDir = WRITEPATH . 'cache/watermarked/';
    // Include watermark size and locked status in cache key
    $lockedSuffix = $isLocked ? '_blurred' : '';
    $sourceVersion = $this->sourceVersion($path);
    $cacheKey = md5($path . $sourceVersion . $watermarkText . '_large' . $lockedSuffix);
    $cacheFile = $cacheDir . $cacheKey;
    
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }

    $cacheTtl = self::IMAGE_BROWSER_CACHE_SECONDS;
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
        $mimeType = mime_content_type($cacheFile) ?: 'image/jpeg';
        $etag = $this->etagFromFile($cacheFile);
        $lastModified = filemtime($cacheFile) ?: time();

        if ($this->clientHasFreshImage($etag, $lastModified)) {
            return $this->imageResponse($mimeType, '', $etag, $lastModified)
                ->setHeader('X-Cache', 'HIT')
                ->setStatusCode(304);
        }

        return $this->imageResponse($mimeType, file_get_contents($cacheFile), $etag, $lastModified)
            ->setHeader('X-Cache', 'HIT');
    }

    // --- BAGIAN 1: AMBIL FILE ---
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($path, ['timeout' => 10]);
            $content = $response->getBody();
            $mimeType = $response->getHeaderLine('Content-Type') ?: 'image/jpeg';
        } catch (\Exception $e) {
            return $this->response->setStatusCode(404);
        }
    } else {
        $decodedPath = rawurldecode($path);
        $fullPath = ROOTPATH . 'public/' . $decodedPath;
        if (file_exists($fullPath)) {
            $content = file_get_contents($fullPath);
            $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
        } else {
            return $this->response->setStatusCode(404);
        }
    }

    if (!$content) return $this->response->setStatusCode(404);

    // --- BAGIAN 2: WATERMARK BESAR (ANTI-SCREENSHOT) ---
    if (!empty($watermarkText) && in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
        
        $image = imagecreatefromstring($content);
        if ($image) {
            $width = imagesx($image);
            $height = imagesy($image);

            // Font path
            $fontPath = FCPATH . 'fonts/ArialBold.ttf'; 
            
            if (file_exists($fontPath)) {
                $text = strtoupper($watermarkText);
                
                // === STRATEGI 1: TILED WATERMARK (Berulang) ===
                $tileColor = imagecolorallocatealpha($image, 255, 255, 255, 50); // Alpha 50 = lebih terlihat
                $fontSize = $width * 0.08; // 8% lebar gambar
                $angle = -45; // Miring negatif
                
                // Hitung ukuran text untuk spacing
                $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);
                $textWidth = abs($bbox[2] - $bbox[0]);
                $textHeight = abs($bbox[1] - $bbox[7]);
                
                // Buat pattern berulang diagonal
                $spacingX = $textWidth + ($width * 0.1); // Jarak horizontal
                $spacingY = $textHeight + ($height * 0.15); // Jarak vertikal
                
                for ($y = $spacingY; $y < $height; $y += $spacingY) {
                    for ($x = 0; $x < $width; $x += $spacingX) {
                        imagettftext($image, $fontSize, $angle, $x, $y, $tileColor, $fontPath, $text);
                    }
                }
                
                // === STRATEGI 2: WATERMARK BESAR DI TENGAH ===
                $centerColor = imagecolorallocatealpha($image, 255, 255, 255, 30); // Alpha 30 = cukup transparan tapi terlihat
                $centerFontSize = min($width, $height) * 0.15; // 15% dari dimensi terkecil (BESAR!)
                $centerAngle = -45;
                
                $bbox = imagettfbbox($centerFontSize, $centerAngle, $fontPath, $text);
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                
                $centerX = ($width - $textWidth) / 2;
                $centerY = ($height + $textHeight) / 2;
                
                imagettftext($image, $centerFontSize, $centerAngle, $centerX, $centerY, $centerColor, $fontPath, $text);
                
                // === STRATEGI 3: WATERMARK DI 4 SUDUT ===
                $cornerColor = imagecolorallocatealpha($image, 255, 255, 255, 60); // Alpha 60 = lebih opaque
                $cornerFontSize = $width * 0.05;
                $cornerText = '© ' . $watermarkText;
                
                // Sudut kiri atas
                imagettftext($image, $cornerFontSize, 0, 20, 40, $cornerColor, $fontPath, $cornerText);
                
                // Sudut kanan atas
                $bbox = imagettfbbox($cornerFontSize, 0, $fontPath, $cornerText);
                $cornerX = $width - ($bbox[2] - $bbox[0]) - 20;
                imagettftext($image, $cornerFontSize, 0, $cornerX, 40, $cornerColor, $fontPath, $cornerText);
                
                // Sudut kiri bawah
                imagettftext($image, $cornerFontSize, 0, 20, $height - 20, $cornerColor, $fontPath, $cornerText);
                
                // Sudut kanan bawah
                imagettftext($image, $cornerFontSize, 0, $cornerX, $height - 20, $cornerColor, $fontPath, $cornerText);
                
            } else {
                // Fallback tanpa TTF
                $color = imagecolorallocatealpha($image, 255, 255, 255, 50);
                $text = strtoupper($watermarkText);
                
                // Tengah besar
                imagestring($image, 5, $width/2 - 100, $height/2 - 20, $text, $color);
                
                // Tiled sederhana
                for ($i = 0; $i < $width; $i += 200) {
                    for ($j = 0; $j < $height; $j += 150) {
                        imagestring($image, 3, $i, $j, $text, $color);
                    }
                }
            }

            // BLUR LOGIC IF LOCKED
            if ($isLocked) {
                // Heavily blur the image
                // Apply gaussian blur multiple times for a stronger effect
                for ($i = 0; $i < 50; $i++) {
                    imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
                }
                // Also scale down and scale back up to pixelate/obfuscate details further
                $blurredWidth = max(1, $width / 10);
                $blurredHeight = max(1, $height / 10);
                $tempImg = imagecreatetruecolor($blurredWidth, $blurredHeight);
                imagecopyresampled($tempImg, $image, 0, 0, 0, 0, $blurredWidth, $blurredHeight, $width, $height);
                imagecopyresampled($image, $tempImg, 0, 0, 0, 0, $width, $height, $blurredWidth, $blurredHeight);
                imagedestroy($tempImg);
            }


            // Simpan ke string
            ob_start();
            if ($mimeType === 'image/png') {
                imagesavealpha($image, true);
                imagepng($image, null, 9);
            } elseif ($mimeType === 'image/webp') {
                imagewebp($image, null, 85);
            } else {
                imagejpeg($image, null, 85);
            }
            
            $content = ob_get_clean();
            imagedestroy($image);
            
            // SAVE TO CACHE
            file_put_contents($cacheFile, $content);
        }
    }

    if (!file_exists($cacheFile)) {
        file_put_contents($cacheFile, $content);
    }

    // --- BAGIAN 3: KIRIM KE BROWSER ---
    $etag = file_exists($cacheFile) ? $this->etagFromFile($cacheFile) : '"' . md5($content) . '"';
    $lastModified = file_exists($cacheFile) ? (filemtime($cacheFile) ?: time()) : time();

    return $this->imageResponse($mimeType, $content, $etag, $lastModified)
        ->setHeader('X-Cache', 'MISS');
}

    private function imageResponse(string $mimeType, string $content, string $etag, int $lastModified, string $visibility = 'private')
    {
        return $this->response
            ->setHeader('Content-Type', $mimeType ?: 'image/jpeg')
            ->setHeader('Cache-Control', $visibility . ', max-age=' . self::IMAGE_BROWSER_CACHE_SECONDS)
            ->setHeader('ETag', $etag)
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $lastModified) . ' GMT')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($content);
    }

    private function clientHasFreshImage(string $etag, int $lastModified): bool
    {
        $ifNoneMatch = trim($this->request->getHeaderLine('If-None-Match'));
        if ($ifNoneMatch !== '' && $ifNoneMatch === $etag) {
            return true;
        }

        $ifModifiedSince = $this->request->getHeaderLine('If-Modified-Since');
        if ($ifModifiedSince === '') {
            return false;
        }

        $clientTime = strtotime($ifModifiedSince);
        return $clientTime !== false && $clientTime >= $lastModified;
    }

    private function etagFromFile(string $filePath): string
    {
        return '"' . md5($filePath . '|' . filesize($filePath) . '|' . filemtime($filePath)) . '"';
    }

    private function sourceVersion(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return '';
        }

        $fullPath = ROOTPATH . 'public/' . rawurldecode($path);
        if (!file_exists($fullPath)) {
            return '';
        }

        return filesize($fullPath) . '|' . filemtime($fullPath);
    }
}
