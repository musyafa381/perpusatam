<?php

if (!function_exists('getTvBannersFile')) {
    function getTvBannersFile(): string
    {
        return WRITEPATH . 'tv_banners.json';
    }
}

if (!function_exists('getTvBanners')) {
    function getTvBanners(): array
    {
        $file = getTvBannersFile();
        if (!file_exists($file)) {
            return [];
        }
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
}

if (!function_exists('saveTvBanners')) {
    function saveTvBanners(array $banners): bool
    {
        $file = getTvBannersFile();
        return (bool) file_put_contents($file, json_encode(array_values($banners), JSON_PRETTY_PRINT));
    }
}

if (!function_exists('addTvBanner')) {
    function addTvBanner(string $url, string $title): bool
    {
        $banners = getTvBanners();
        $id = 'banner_' . time() . '_' . substr(md5(uniqid()), 0, 6);
        $banners[] = [
            'id'         => $id,
            'title'      => !empty($title) ? $title : 'Banner TV Perpus',
            'url'        => $url,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return saveTvBanners($banners);
    }
}

if (!function_exists('moveTvBanner')) {
    function moveTvBanner(string $id, string $direction): bool
    {
        $banners = getTvBanners();
        $idx = -1;
        foreach ($banners as $index => $b) {
            if (isset($b['id']) && $b['id'] === $id) {
                $idx = $index;
                break;
            }
        }

        if ($idx === -1) {
            return false;
        }

        if ($direction === 'up' && $idx > 0) {
            $temp = $banners[$idx];
            $banners[$idx] = $banners[$idx - 1];
            $banners[$idx - 1] = $temp;
        } elseif ($direction === 'down' && $idx < count($banners) - 1) {
            $temp = $banners[$idx];
            $banners[$idx] = $banners[$idx + 1];
            $banners[$idx + 1] = $temp;
        }

        return saveTvBanners($banners);
    }
}

if (!function_exists('deleteTvBanner')) {
    function deleteTvBanner(string $id): bool
    {
        $banners = getTvBanners();
        $filtered = array_filter($banners, function ($b) use ($id) {
            return isset($b['id']) && $b['id'] !== $id;
        });
        return saveTvBanners(array_values($filtered));
    }
}
