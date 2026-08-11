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

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $targetDir = FCPATH . 'uploads/tv';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($targetDir, $newName);
            $localPath = $targetDir . DIRECTORY_SEPARATOR . $newName;

            // Try Cloudinary upload first
            $cloudUrl = uploadToCloudinary($localPath, 'perpustakaan/tv');

            if (!empty($cloudUrl)) {
                @unlink($localPath);
                $finalUrl = $cloudUrl;
            } else {
                // Fallback to local uploads if Cloudinary returns null
                $finalUrl = base_url('uploads/tv/' . $newName);
            }
        }

        // Check if URL field provided instead
        if (empty($finalUrl)) {
            $urlInput = trim((string) $this->request->getPost('poster_url'));
            if (!empty($urlInput)) {
                $finalUrl = $urlInput;
            }
        }

        if (!empty($finalUrl)) {
            addTvBanner($finalUrl, $titleInput);
            return redirect()->to(base_url('admin/tv-content'))->with('message', 'Banner TV Perpus berhasil ditambahkan!');
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
