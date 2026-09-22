<?php
// app/Helpers/FinfoPolyfill.php
// Polyfill for PHP ext-fileinfo when disabled or missing in web server environment

if (!defined('FILEINFO_NONE')) {
    define('FILEINFO_NONE', 0);
}
if (!defined('FILEINFO_SYMLINK')) {
    define('FILEINFO_SYMLINK', 2);
}
if (!defined('FILEINFO_MIME')) {
    define('FILEINFO_MIME', 1040);
}
if (!defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (!defined('FILEINFO_MIME_ENCODING')) {
    define('FILEINFO_MIME_ENCODING', 1024);
}
if (!defined('FILEINFO_DEVICES')) {
    define('FILEINFO_DEVICES', 8);
}
if (!defined('FILEINFO_CONTINUE')) {
    define('FILEINFO_CONTINUE', 32);
}
if (!defined('FILEINFO_PRESERVE_ATIME')) {
    define('FILEINFO_PRESERVE_ATIME', 128);
}
if (!defined('FILEINFO_RAW')) {
    define('FILEINFO_RAW', 256);
}
if (!defined('FILEINFO_EXTENSION')) {
    define('FILEINFO_EXTENSION', 16777216);
}

if (!class_exists('finfo', false)) {
    class finfo
    {
        private static array $mimeMap = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip'  => 'application/zip',
            'rar'  => 'application/x-rar-compressed',
            '7z'   => 'application/x-7z-compressed',
            'tar'  => 'application/x-tar',
            'gz'   => 'application/gzip',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
            'json' => 'application/json',
            'xml'  => 'application/xml',
            'html' => 'text/html',
            'css'  => 'text/css',
            'js'   => 'application/javascript',
        ];

        public function __construct(int $flags = FILEINFO_MIME_TYPE, ?string $magic_file = null)
        {
        }

        public function file(?string $filename = null, int $flags = FILEINFO_MIME_TYPE, $context = null): string|false
        {
            if (empty($filename)) {
                return false;
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            return self::$mimeMap[$ext] ?? 'application/octet-stream';
        }

        public function buffer(?string $string = null, int $flags = FILEINFO_MIME_TYPE, $context = null): string|false
        {
            if ($string === null) {
                return false;
            }

            if (str_starts_with($string, '%PDF-')) {
                return 'application/pdf';
            }
            if (str_starts_with($string, "\x89PNG\r\n\x1a\n")) {
                return 'image/png';
            }
            if (str_starts_with($string, "\xFF\xD8\xFF")) {
                return 'image/jpeg';
            }
            if (str_starts_with($string, "GIF87a") || str_starts_with($string, "GIF89a")) {
                return 'image/gif';
            }
            if (str_starts_with($string, "PK\x03\x04")) {
                return 'application/zip';
            }

            return 'application/octet-stream';
        }

        public function set_flags(int $flags): bool
        {
            return true;
        }
    }
}

if (!function_exists('finfo_open')) {
    function finfo_open(int $flags = FILEINFO_MIME_TYPE, ?string $magic_file = null)
    {
        return new \finfo($flags, $magic_file);
    }
}

if (!function_exists('finfo_file')) {
    function finfo_file($finfo, ?string $filename = null, int $flags = FILEINFO_MIME_TYPE, $context = null)
    {
        return $finfo instanceof \finfo ? $finfo->file($filename, $flags, $context) : 'application/octet-stream';
    }
}

if (!function_exists('finfo_buffer')) {
    function finfo_buffer($finfo, ?string $string = null, int $flags = FILEINFO_MIME_TYPE, $context = null)
    {
        return $finfo instanceof \finfo ? $finfo->buffer($string, $flags, $context) : 'application/octet-stream';
    }
}

if (!function_exists('finfo_close')) {
    function finfo_close($finfo): bool
    {
        return true;
    }
}
