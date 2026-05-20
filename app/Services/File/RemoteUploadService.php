<?php

namespace App\Services\File;

use CodeIgniter\HTTP\Files\UploadedFile;

class RemoteUploadService
{
    // The endpoint where you uploaded the upload.php script
    protected string $receiverUrl = 'https://z-enterprise.my.id/image-nusashare/upload.php';
    protected string $token       = 'NusaShare_Secure_992211_!@#'; // Gunakan token ini juga di upload.php remote

    /**
     * Upload file to remote server
     * 
     * @param UploadedFile $file
     * @param string $type 'arts' or 'cover'
     * @return string|null Full URL on success, null on failure
     */
    public function upload(UploadedFile $file, string $type): ?string
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ch = curl_init();

        $data = [
            'token' => $this->token,
            'type'  => $type,
            'image' => new \CURLFile($file->getTempName(), $file->getClientMimeType(), $file->getClientName())
        ];

        curl_setopt($ch, CURLOPT_URL, $this->receiverUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Adjust based on remote SSL setup

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', 'Remote upload error: ' . $err);
            return null;
        }

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] === 'success') {
            return $result['url'];
        }

        log_message('error', 'Remote upload failed: ' . ($result['message'] ?? 'Unknown error'));
        return null;
    }
}
