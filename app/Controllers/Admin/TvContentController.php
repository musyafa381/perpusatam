<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class TvContentController extends BaseController
{
    public function index()
    {
        helper(['upload', 'tv_helper', 'form']);

        $data = [
            'title'   => 'Konten TV Perpus',
            'banners' => getTvBanners()
        ];

        return view('admin/tv_content/index', $data);
    }

    public function store()
    {
        helper(['upload', 'tv_helper']);

        $titleInput = trim((string) $this->request->getPost('title'));
        $file = $this->request->getFile('poster');
        $finalUrl = null;

        try {
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Try direct upload from temp path to Cloudinary first
                $tempPath = $file->getTempName();
                if (!empty($tempPath) && file_exists($tempPath)) {
                    $cloudUrl = uploadToCloudinary($tempPath, 'perpustakaan/tv');
                    if (!empty($cloudUrl)) {
                        $finalUrl = $cloudUrl;
                    }
                }

                // If Cloudinary didn't return a URL, fallback to local uploads directory
                if (empty($finalUrl)) {
                    $targetDir = FCPATH . 'uploads/tv';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    $newName = $file->getRandomName();
                    if (@$file->move($targetDir, $newName)) {
                        $finalUrl = base_url('uploads/tv/' . $newName);
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'TvContentController store file error: ' . $e->getMessage());
        }

        // Check if URL field provided instead
        if (empty($finalUrl)) {
            $urlInput = trim((string) $this->request->getPost('poster_url'));
            if (!empty($urlInput)) {
                $finalUrl = $urlInput;
            }
        }

        if (!empty($finalUrl)) {
            try {
                addTvBanner($finalUrl, $titleInput);
                return redirect()->to(base_url('admin/tv-content'))->with('message', 'Banner TV Perpus berhasil ditambahkan!');
            } catch (\Throwable $e) {
                log_message('error', 'addTvBanner error: ' . $e->getMessage());
                return redirect()->to(base_url('admin/tv-content'))->with('error', 'Gagal menyimpan banner: ' . $e->getMessage());
            }
        }

        return redirect()->to(base_url('admin/tv-content'))->with('error', 'Silakan pilih file gambar banner atau tempelkan URL gambar terlebih dahulu.');
    }

    public function move($id, $direction)
    {
        helper(['upload', 'tv_helper']);
        moveTvBanner((string) $id, (string) $direction);
        return redirect()->to(base_url('admin/tv-content'))->with('message', 'Urutan banner TV berhasil diperbarui!');
    }

    public function delete($id)
    {
        helper(['upload', 'tv_helper']);
        deleteTvBanner((string) $id);
        return redirect()->to(base_url('admin/tv-content'))->with('message', 'Banner TV Perpus berhasil dihapus.');
    }
}
