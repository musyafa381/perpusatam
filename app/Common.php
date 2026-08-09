<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('getBookCover')) {
    function getBookCover(?string $coverName = null): string
    {
        if (empty($coverName)) {
            $default = (defined('BOOK_COVER_URI') && defined('DEFAULT_BOOK_COVER'))
                ? BOOK_COVER_URI . DEFAULT_BOOK_COVER
                : 'assets/images/cover/default.jpg';
            return base_url($default);
        }

        if (str_starts_with($coverName, 'http://') || str_starts_with($coverName, 'https://')) {
            return $coverName;
        }

        if (defined('BOOK_COVER_PATH') && defined('BOOK_COVER_URI') && defined('DEFAULT_BOOK_COVER')) {
            $path = BOOK_COVER_PATH . $coverName;
            if (file_exists($path)) {
                return base_url(BOOK_COVER_URI . $coverName);
            }
            $localPath = BOOK_COVER_PATH . DIRECTORY_SEPARATOR . $coverName;
            if (file_exists($localPath)) {
                return base_url(BOOK_COVER_URI . $coverName);
            }
            return base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER);
        }

        return base_url('assets/images/cover/' . $coverName);
    }
}

if (!function_exists('generateBarcodeSVG')) {
    /**
     * Generate a crisp, wide-spaced, scanner-friendly Code 128-B SVG barcode
     */
    function generateBarcodeSVG(?string $code = '', int $height = 55): string
    {
        // Code 128 pattern dictionary (symbols 0 to 106 - ISO/IEC 15417 compliant)
        $c128 = [
            '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213', // 0-9
            '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132', // 10-19
            '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211', // 20-29
            '212123','212321','232121','111323','131123','131321','112313','132113','132311','113312', // 30-39
            '133112','133211','311312','331112','331211','112133','112331','132131','113123','113321', // 40-49
            '133121','313112','331121','312113','312311','332111','314111','221411','431111','111224', // 50-59
            '111422','121124','121421','141122','141221','112214','112412','122114','122411','142112', // 60-69
            '142211','141212','142121','141221','211214','211412','213112','213211','241112','131124', // 70-79
            '131312','114112','114211','121142','121241','114221','124112','124211','411131','411311', // 80-89
            '431113','511111','111431','311141','111341','131141','114113','114311','411113','411311', // 90-99
            '113141','114131','311141','211412','211214','211232','2331112'                            // 100-106 (103:StartA, 104:StartB, 105:StartC, 106:Stop)
        ];

        $code = trim((string)$code);
        if (empty($code)) {
            $code = '00000000';
        }

        $indices = [104]; // Start Code B
        $checksum = 104;

        $len = strlen($code);
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($code[$i]);
            $idx = $ascii - 32;
            if ($idx < 0 || $idx > 94) {
                $idx = 0;
            }
            $indices[] = $idx;
            $checksum += $idx * ($i + 1);
        }

        $checkIdx = $checksum % 103;
        $indices[] = $checkIdx;
        $indices[] = 106; // Stop Code

        $widths = '';
        foreach ($indices as $idx) {
            $widths .= $c128[$idx];
        }

        $moduleWidth = 2;
        $quietZone = 18; // Quiet Zone margin left & right
        $totalBarModules = 0;
        $wLen = strlen($widths);
        for ($j = 0; $j < $wLen; $j++) {
            $totalBarModules += (int)$widths[$j];
        }
        $totalWidth = ($totalBarModules * $moduleWidth) + ($quietZone * 2);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $totalWidth . ' ' . $height . '" width="100%" height="' . $height . 'px" preserveAspectRatio="xMidYMid meet" style="background:#ffffff; display:block; margin:0 auto; padding:0;">';
        $svg .= '<rect x="0" y="0" width="' . $totalWidth . '" height="' . $height . '" fill="#ffffff"/>';

        $posX = $quietZone;
        $isBar = true;
        for ($j = 0; $j < $wLen; $j++) {
            $w = (int)$widths[$j] * $moduleWidth;
            if ($isBar) {
                $svg .= '<rect x="' . $posX . '" y="0" width="' . $w . '" height="' . $height . '" fill="#000000"/>';
            }
            $posX += $w;
            $isBar = !$isBar;
        }
        $svg .= '</svg>';
        return $svg;
    }
}

if (!function_exists('generateQRCodeSVG')) {
    /**
     * Generate a crisp 2D QR Code SVG string (scannable by HP cameras and 2D barcode scanners)
     */
    function generateQRCodeSVG(?string $code = '', int $size = 90): string
    {
        $code = trim((string)$code);
        if (empty($code)) {
            $code = '00000000';
        }

        try {
            if (class_exists('\Endroid\QrCode\QrCode')) {
                $qrCode = \Endroid\QrCode\QrCode::create($code)
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow())
                    ->setSize($size)
                    ->setMargin(4);

                $writer = new \Endroid\QrCode\Writer\SvgWriter();
                $result = $writer->write($qrCode);
                $svg = $result->getString();

                // Remove XML declaration for clean inline embedding
                $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
                return trim($svg);
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $size . 'px" height="' . $size . 'px"><rect width="100%" height="100%" fill="#ffffff"/><text x="50%" y="50%" text-anchor="middle" font-size="10">QR Code</text></svg>';
    }
}
