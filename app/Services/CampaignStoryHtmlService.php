<?php

namespace App\Services;

/**
 * Converts embedded raster data-URL images in HTML (e.g. campaign story) into files
 * under public/assets/images/editor — same location as admin CKEditor uploads — and
 * rewrites img src to a public URL.
 */
class CampaignStoryHtmlService
{
    private const MAX_IMAGES_PER_SAVE = 200;

    public static function replaceDataUrlImagesWithStoredFiles(?string $html): string
    {
        $html = (string) $html;
        if ($html === '' || stripos($html, 'data:image') === false) {
            return $html;
        }

        $pattern = '/<img\b[^>]*\bsrc\s*=\s*(["\'])(data:image\/(?:png|jpeg|pjpeg|jpg|gif|webp);base64,[^"\']*)\1[^>]*>/iu';

        for ($i = 0; $i < self::MAX_IMAGES_PER_SAVE; $i++) {
            if (! preg_match($pattern, $html)) {
                break;
            }

            $html = (string) preg_replace_callback(
                $pattern,
                function (array $m) {
                    $quote = $m[1];
                    $dataUrl = $m[2];
                    $fullTag = $m[0];
                    $fileUrl = self::persistRasterDataUrlAsPublicFile($dataUrl);
                    if (! $fileUrl) {
                        return $fullTag;
                    }

                    return preg_replace(
                        '/\bsrc\s*=\s*(["\']).*?\1/iu',
                        'src=' . $quote . $fileUrl . $quote,
                        $fullTag,
                        1
                    );
                },
                $html,
                1
            );
        }

        return $html;
    }

    private static function persistRasterDataUrlAsPublicFile(string $dataUrl): ?string
    {
        if (! preg_match('#^data:image/(png|jpeg|pjpeg|jpg|gif|webp);base64,(.+)$#is', $dataUrl, $m)) {
            return null;
        }

        $b64 = preg_replace('/\s+/', '', $m[2] ?? '');
        $binary = base64_decode($b64, true);
        if ($binary === false || $binary === '') {
            return null;
        }

        $maxBytes = 5120 * 1024;
        if (strlen($binary) > $maxBytes) {
            return null;
        }

        $info = @getimagesizefromstring($binary);
        if ($info === false) {
            return null;
        }

        $extMap = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
        ];
        $ext = $extMap[$info[2] ?? 0] ?? null;
        if (! $ext) {
            return null;
        }

        $fileName = time() . '_' . uniqid('', true) . '.' . $ext;
        $uploadPath = public_path('assets/images/editor');
        if (! is_dir($uploadPath)) {
            @mkdir($uploadPath, 0775, true);
        }

        $full = $uploadPath . '/' . $fileName;
        if (file_put_contents($full, $binary) === false) {
            return null;
        }

        @chmod($full, 0644);

        return asset('assets/images/editor/' . $fileName);
    }
}
