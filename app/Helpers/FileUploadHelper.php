<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * Standard allowed document extensions.
     */
    public static function allowedDocumentExtensions(): array
    {
        return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'vsd', 'vsdx', 'zip', 'rar', '7z', 'tar', 'gz', 'png', 'jpg', 'jpeg', 'webp', 'txt'];
    }

    /**
     * Get a safe validation rule array that checks file extensions without failing on server finfo MIME discrepancies.
     */
    public static function fileValidationRule(int $maxKilobytes = 51200, array $customExtensions = []): array
    {
        $allowed = !empty($customExtensions) ? $customExtensions : self::allowedDocumentExtensions();
        return [
            'nullable',
            'file',
            'max:' . $maxKilobytes,
            function ($attribute, $value, $fail) use ($allowed) {
                if ($value && $value->isValid()) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!$ext || !in_array($ext, $allowed)) {
                        $fail('Format file yang didukung: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), PPT/PPTX, Visio (VSD/VSDX), Gambar (PNG/JPG), dan Arsip ZIP/RAR.');
                    }
                }
            }
        ];
    }

    /**
     * Store an uploaded file safely across local and shared hosting (cPanel/VPS) environments
     * without crashing when PHP ext-fileinfo is disabled or missing.
     *
     * @param UploadedFile|null $file
     * @param string $folder Relative directory under storage/app/public (e.g. 'bdm_handovers')
     * @return string|null Relative path from public disk (e.g. 'bdm_handovers/1726555200_abcdef1234.pdf')
     */
    public static function storePublicly(?UploadedFile $file, string $folder): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $folder = trim($folder, '/\\');
        $ext = strtolower($file->getClientOriginalExtension());
        $cleanExt = $ext ? '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext) : '';
        $fileName = time() . '_' . Str::random(12) . $cleanExt;

        // 1. Destination storage/app/public/...
        $destAppPublic = storage_path('app/public/' . $folder);
        if (!file_exists($destAppPublic)) {
            @mkdir($destAppPublic, 0755, true);
        }

        // 2. Destination public/storage/...
        $destPublic = public_path('storage/' . $folder);
        if (!file_exists($destPublic)) {
            @mkdir($destPublic, 0755, true);
        }

        // 3. Destination ../public_html/storage/... (for cPanel separate document roots)
        $destPublicHtml = base_path('../public_html/storage/' . $folder);
        $hasPublicHtml = is_dir(base_path('../public_html'));
        if ($hasPublicHtml && !file_exists($destPublicHtml)) {
            @mkdir($destPublicHtml, 0755, true);
        }

        // Move uploaded file to primary destination
        $file->move($destAppPublic, $fileName);
        $sourceFile = $destAppPublic . '/' . $fileName;

        // Copy to public/storage if it's a distinct physical directory (not a symlink)
        if (file_exists($destPublic) && realpath($destPublic) !== realpath($destAppPublic)) {
            @copy($sourceFile, $destPublic . '/' . $fileName);
        }

        // Copy to public_html/storage if cPanel folder exists
        if ($hasPublicHtml && file_exists($destPublicHtml)) {
            @copy($sourceFile, $destPublicHtml . '/' . $fileName);
        }

        return $folder . '/' . $fileName;
    }

    /**
     * Get public URL for a stored file without triggering Flysystem / finfo.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return asset('storage/' . ltrim($path, '/\\'));
    }

    /**
     * Check if a stored file exists without triggering Flysystem / finfo.
     */
    public static function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        $relPath = ltrim($path, '/\\');
        return file_exists(storage_path('app/public/' . $relPath))
            || file_exists(public_path('storage/' . $relPath))
            || (is_dir(base_path('../public_html')) && file_exists(base_path('../public_html/storage/' . $relPath)));
    }

    /**
     * Safely delete a file from all storage locations.
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        $relPath = ltrim($path, '/\\');
        $deleted = false;

        $p1 = storage_path('app/public/' . $relPath);
        if (file_exists($p1)) {
            @unlink($p1);
            $deleted = true;
        }

        $p2 = public_path('storage/' . $relPath);
        if (file_exists($p2) && realpath($p2) !== realpath($p1)) {
            @unlink($p2);
            $deleted = true;
        }

        $p3 = base_path('../public_html/storage/' . $relPath);
        if (is_dir(base_path('../public_html')) && file_exists($p3)) {
            @unlink($p3);
            $deleted = true;
        }

        return $deleted;
    }
}
