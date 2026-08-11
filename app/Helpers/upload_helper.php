<?php

/**
 * Get Cloudinary configuration array from env with automatic fallback defaults
 */
function getCloudinaryConfig(): array
{
    $cloudName = trim((string)env('CLOUDINARY_CLOUD_NAME', 'p9srrllp'), "'\" \t\n\r");
    if (empty($cloudName) || $cloudName === 'isi_cloud_name_di_sini') {
        $cloudName = 'p9srrllp';
    }

    $apiKey = trim((string)env('CLOUDINARY_API_KEY', '997438152812494'), "'\" \t\n\r");
    if (empty($apiKey)) {
        $apiKey = '997438152812494';
    }

    $apiSecret = trim((string)env('CLOUDINARY_API_SECRET', 'rrZ9HXa1DI3LkutcejkC9_kR-TU'), "'\" \t\n\r");
    if (empty($apiSecret)) {
        $apiSecret = 'rrZ9HXa1DI3LkutcejkC9_kR-TU';
    }

    return [
        'cloud_name' => $cloudName,
        'api_key'    => $apiKey,
        'api_secret' => $apiSecret,
    ];
}

/**
 * Upload local file or temp file to Cloudinary cloud storage
 */
function uploadToCloudinary(string $realFilePath, string $folder = 'perpustakaan/covers', string $originalName = ''): string|null
{
    $config = getCloudinaryConfig();
    if (empty($config['cloud_name']) || empty($config['api_key']) || empty($config['api_secret'])) {
        log_message('error', 'Cloudinary Config Empty: ' . json_encode($config));
        return null;
    }

    if (empty($realFilePath) || !file_exists($realFilePath)) {
        log_message('error', 'Cloudinary File Not Found: ' . $realFilePath);
        return null;
    }

    $absPath = realpath($realFilePath) ?: $realFilePath;
    $timestamp = time();
    $toSign = "folder={$folder}&timestamp={$timestamp}" . $config['api_secret'];
    $signature = sha1($toSign);

    $mimeType = function_exists('mime_content_type') ? (@mime_content_type($absPath) ?: 'image/jpeg') : 'image/jpeg';
    $postFileName = !empty($originalName) ? basename($originalName) : basename($absPath);
    if (!str_contains($postFileName, '.')) {
        $ext = ($mimeType === 'image/png') ? '.png' : (($mimeType === 'image/webp') ? '.webp' : '.jpg');
        $postFileName .= $ext;
    }

    $cfile = new \CURLFile($absPath, $mimeType, $postFileName);

    $postData = [
        'file'      => $cfile,
        'api_key'   => $config['api_key'],
        'timestamp' => $timestamp,
        'signature' => $signature,
        'folder'    => $folder,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => "https://api.cloudinary.com/v1_1/{$config['cloud_name']}/image/upload",
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 45,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) CodeIgniter-Cloudinary-Uploader',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300 && !empty($response)) {
        $json = json_decode($response, true);
        if (!empty($json['secure_url'])) {
            return $json['secure_url'];
        }
    }

    log_message('error', "Cloudinary Upload Failed (HTTP {$httpCode}): {$curlErr} | Response: {$response}");
    return null;
}

/**
 * Automatically resize & crop book cover to fixed standard dimension (600x900px, 2:3 ratio)
 */
function optimizeAndResizeBookCover(string $filePath, int $width = 600, int $height = 900): bool
{
    if (empty($filePath) || !file_exists($filePath)) {
        return false;
    }

    try {
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            return false;
        }
        $image = \Config\Services::image();
        $image->withFile($filePath)
              ->fit($width, $height, 'center')
              ->save($filePath, 85);
        return true;
    } catch (\Throwable $e) {
        log_message('error', 'Image Resize Failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Save book cover image directly to Cloudinary (or fallback to local disk)
 */
function uploadBookCover(\CodeIgniter\HTTP\Files\UploadedFile|null $coverImage): string|null
{
    if (!$coverImage || !$coverImage->isValid() || $coverImage->hasMoved()) {
        return null;
    }

    $tempPath = $coverImage->getTempName();

    // 1. Direct Cloudinary upload from temp file (No local folder permission requirement!)
    if (!empty($tempPath) && file_exists($tempPath)) {
        try {
            $cloudinaryUrl = uploadToCloudinary($tempPath, 'perpustakaan/covers', $coverImage->getClientName());
            if (!empty($cloudinaryUrl)) {
                return $cloudinaryUrl;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Cloudinary direct upload failed: ' . $e->getMessage());
        }
    }

    // 2. Fallback to local storage if Cloudinary upload failed
    try {
        $targetDir = rtrim(BOOK_COVER_PATH, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $coverImageFileName = $coverImage->getRandomName();
        $save = $coverImage->move($targetDir, $coverImageFileName);

        if ($save) {
            $localPath = $targetDir . $coverImageFileName;
            @optimizeAndResizeBookCover($localPath, 600, 900);
            return $coverImageFileName;
        }
    } catch (\Throwable $e) {
        log_message('error', 'uploadBookCover local fallback failed: ' . $e->getMessage());
    }

    return null;
}

/**
 * Download book cover from external URL and save directly to Cloudinary
 */
function downloadBookCoverFromUrl(string $url): string|null
{
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }

    // Download image data via cURL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) CodeIgniter-Cloudinary-Uploader',
    ]);

    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300 && !empty($imageData)) {
        // Save to system temp directory
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'remote_cover_' . uniqid() . '.jpg';
        if (file_put_contents($tempFile, $imageData)) {
            // Upload temp file directly to Cloudinary
            $cloudinaryUrl = uploadToCloudinary($tempFile, 'perpustakaan/covers', 'remote_cover.jpg');
            @unlink($tempFile);
            if (!empty($cloudinaryUrl)) {
                return $cloudinaryUrl;
            }
        }

        // Local fallback if temp/Cloudinary fails
        $targetDir = rtrim(BOOK_COVER_PATH, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        $filename = 'cover_remote_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.jpg';
        $savePath = $targetDir . $filename;
        if (@file_put_contents($savePath, $imageData)) {
            @optimizeAndResizeBookCover($savePath, 600, 900);
            return $filename;
        }
    }

    return null;
}

/**
 * Get full accessible cover image URL (handles Cloudinary HTTPS URLs & local filenames)
 */
if (!function_exists('getBookCoverUrl')) {
    function getBookCoverUrl(string|null $coverImageFileName): string
    {
        return getBookCover($coverImageFileName);
    }
}

if (!function_exists('getBookCover')) {
    function getBookCover(string|null $coverImageFileName): string
    {
        if (empty($coverImageFileName)) {
            return base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER);
        }

        // If it is already a full Cloudinary or external HTTPS URL
        if (filter_var($coverImageFileName, FILTER_VALIDATE_URL)) {
            return $coverImageFileName;
        }

        // If file exists in local storage
        $localFile = rtrim(BOOK_COVER_PATH, '/\\') . DIRECTORY_SEPARATOR . $coverImageFileName;
        if (file_exists($localFile)) {
            return base_url(BOOK_COVER_URI . $coverImageFileName);
        }

        return base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER);
    }
}

/**
 * Delete a loan QR code file from local storage.
 * Accepts the stored qr_code value (filename or full URL).
 * Only deletes local files — skips Cloudinary / external URLs.
 */
if (!function_exists('deleteLoansQRCode')) {
    function deleteLoansQRCode(string|null $qrCodeValue): bool
    {
        if (empty($qrCodeValue)) {
            return false;
        }

        // Skip deletion if it's an external / Cloudinary URL
        if (filter_var($qrCodeValue, FILTER_VALIDATE_URL)) {
            return false;
        }

        $filePath = rtrim(LOANS_QR_CODE_PATH, '/\\') . DIRECTORY_SEPARATOR . $qrCodeValue;
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }

        return false;
    }
}

/**
 * Delete a book cover file from local storage.
 * Skips deletion if it's an external URL (e.g. Cloudinary).
 */
if (!function_exists('deleteBookCover')) {
    function deleteBookCover(string|null $coverImageFileName): bool
    {
        if (empty($coverImageFileName)) {
            return false;
        }

        // Skip if it's a full external / Cloudinary URL
        if (filter_var($coverImageFileName, FILTER_VALIDATE_URL)) {
            return false;
        }

        $filePath = rtrim(BOOK_COVER_PATH, '/\\') . DIRECTORY_SEPARATOR . $coverImageFileName;
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }

        return false;
    }
}

/**
 * Update a book cover image: upload new cover and delete the old one.
 * Returns the new cover filename/URL, or the former one if no update occurred.
 */
if (!function_exists('updateBookCover')) {
    function updateBookCover(
        \CodeIgniter\HTTP\Files\UploadedFile|null $newCoverImage,
        string|null $formerCoverImageFileName = null
    ): string|null {
        if (!$newCoverImage || !$newCoverImage->isValid() || $newCoverImage->hasMoved()) {
            return $formerCoverImageFileName;
        }

        $newFileName = uploadBookCover($newCoverImage);

        if ($newFileName) {
            // Delete the old cover after successfully saving the new one
            deleteBookCover($formerCoverImageFileName);
        }

        return $newFileName ?? $formerCoverImageFileName;
    }
}
