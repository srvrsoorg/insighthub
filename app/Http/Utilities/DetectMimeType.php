<?php

namespace App\Http\Utilities;

class DetectMimeType {
    private static array $mime_types = [
        'aac' => 'audio/aac',
        'abw' => 'application/x-abiword',
        'arc' => 'application/x-freearc',
        'avif' => 'image/avif',
        'avi' => 'video/x-msvideo',
        'azw' => 'application/vnd.amazon.ebook',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'bz' => 'application/x-bzip',
        'bz2' => 'application/x-bzip2',
        'cda' => 'application/x-cdf',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'eot' => 'application/vnd.ms-fontobject',
        'epub' => 'application/epub+zip',
        'gz' => 'application/gzip',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/vnd.microsoft.icon',
        'ics' => 'text/calendar',
        'jar' => 'application/java-archive',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'jsonld' => 'application/ld+json',
        'mid' => 'audio/midi audio/x-midi',
        'midi' => 'audio/midi audio/x-midi',
        'mjs' => 'text/javascript',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'mpkg' => 'application/vnd.apple.installer+xml',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'oga' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'ogx' => 'application/ogg',
        'opus' => 'audio/opus',
        'otf' => 'font/otf',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
        'php' => 'application/x-httpd-php',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'rar' => 'application/vnd.rar',
        'rtf' => 'application/rtf',
        'sh' => 'application/x-sh',
        'svg' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        'tar' => 'application/x-tar',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'ts' => 'video/mp2t',
        'ttf' => 'font/ttf',
        'txt' => 'text/plain',
        'vsd' => 'application/vnd.visio',
        'wav' => 'audio/wav',
        'weba' => 'audio/webm',
        'webm' => 'video/webm',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml' => 'text/xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'zip' => 'application/zip',
        '3gp' => 'video/3gpp',
        '3g2' => 'video/3gpp2',
        '7z' => 'application/x-7z-compressed'
    ];

    public static function getMimeTypes(): array
    {
        return self::$mime_types;
    }

    public static function addMimeType(array $types): void
    {
        self::$mime_types = array_merge(self::getMimeTypes(), $types);
    }

    public static function getExtension(string $file): string
    {
        // Use a regular expression to extract the file extension
        $pattern = '/\\.([a-zA-Z0-9]+)(\\?.*)?$/';
        preg_match($pattern, $file, $matches);

        if (isset($matches[1])) {
            return strtolower($matches[1]);
        }

        return '';
    }

    public static function hasExtension(string $extension, string $file): bool
    {
        return self::getExtension($file) == $extension;
    }

    public static function fromExtension(string $extension, string $default = 'application/octet-stream'): string
    {
        if (array_key_exists($extension, self::$mime_types)) {
            return self::$mime_types[$extension];
        }

        return $default;
    }

    public static function fromFile(string $file, string $default = 'application/octet-stream'): string
    {
        $extension = self::getExtension($file);

        if (array_key_exists($extension, self::$mime_types)) {
            return self::$mime_types[$extension];
        }

        return $default;
    }

    public static function getDocumentType(string $mime): string
    {
        $categories = [
            'document' => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.text', 'application/rtf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'image' => ['image/avif', 'image/bmp', 'image/gif', 'image/vnd.microsoft.icon', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/tiff', 'image/webp'],
            'audio' => ['audio/aac', 'audio/midi audio/x-midi', 'audio/mpeg', 'audio/ogg', 'audio/opus', 'audio/vnd.ms-fontobject', 'audio/wav', 'audio/webm'],
            'video' => ['video/x-msvideo', 'video/mp4', 'video/mpeg', 'video/ogg', 'video/mp2t', 'video/3gpp', 'video/3gpp2', 'application/x-shockwave-flash'],
            'text' => ['application/x-abiword', 'text/css', 'text/csv', 'text/html', 'text/javascript', 'application/json', 'application/ld+json', 'text/calendar', 'application/x-httpd-php', 'application/x-cdf', 'text/plain', 'text/xml'],
            'font' => ['font/otf', 'font/ttf', 'font/woff', 'font/woff2'],
            'archive' => ['application/x-freearc', 'application/x-bzip', 'application/x-bzip2', 'application/gzip', 'application/java-archive', 'application/vnd.rar', 'application/x-tar', 'application/zip', 'application/x-7z-compressed'],
            'miscellaneous' => ['application/vnd.amazon.ebook', 'application/vnd.apple.installer+xml', 'application/xhtml+xml', 'application/vnd.visio', 'application/octet-stream', 'application/x-csh', 'application/vnd.mozilla.xul+xml'],
        ];

        foreach ($categories as $category => $mimeTypes) {
            if (in_array($mime, $mimeTypes)) {
                return $category;
            }
        }

        return 'unknown';
    }
}