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
    function generateBarcodeSVG(?string $code = '', int $height = 55): string
    {
        $code = strtoupper(trim((string)$code));
        if (empty($code)) {
            $code = '00000000';
        }

        $patterns = [
            '0' => '101001101101', '1' => '110100101011', '2' => '101100101011', '3' => '110110010101',
            '4' => '101001101011', '5' => '110100110101', '6' => '101100110101', '7' => '101001011011',
            '8' => '110100101101', '9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
            'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101', 'F' => '101101100101',
            'G' => '101010011011', 'H' => '110101001101', 'I' => '101101001101', 'J' => '101011001101',
            'K' => '110101010011', 'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
            'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011', 'R' => '110101011001',
            'S' => '101101011001', 'T' => '101011011001', 'U' => '110010101011', 'V' => '100110101011',
            'W' => '110011010101', 'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
            '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101', '*' => '100101101101',
            '$' => '100100100101', '/' => '100100101001', '+' => '100101001001', '%' => '100100100101'
        ];

        $cleanCode = '*' . preg_replace('/[^A-Z0-9\-\.\ \$\/\+\%]/', '', $code) . '*';
        $bitString = '';
        for ($i = 0; $i < strlen($cleanCode); $i++) {
            $char = $cleanCode[$i];
            if (isset($patterns[$char])) {
                $bitString .= $patterns[$char] . '0';
            }
        }

        $width = strlen($bitString) * 2;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" height="' . $height . 'px" preserveAspectRatio="none" style="background:#ffffff; border-radius: 4px; padding: 4px;">';
        $x = 0;
        for ($i = 0; $i < strlen($bitString); $i++) {
            if ($bitString[$i] === '1') {
                $svg .= '<rect x="' . $x . '" y="0" width="2" height="' . $height . '" fill="#0f172a"/>';
            }
            $x += 2;
        }
        $svg .= '</svg>';
        return $svg;
    }
}
