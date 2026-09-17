<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
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
}
