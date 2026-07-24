<?php

declare(strict_types=1);

namespace App\Core;

final class Vite
{
    private const MANIFEST_PATH = '/public/build/.vite/manifest.json';
    private const DEV_SERVER_URL = 'https://blogy.ddev.site:5173';

    public static function tags(string $entryPoint, string $basePath): string
    {
        $manifestPath = $basePath . self::MANIFEST_PATH;

        if (is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            $entry = $manifest[$entryPoint] ?? null;

            if (is_array($entry)) {
                $tags = [];

                if (isset($entry['css']) && is_array($entry['css'])) {
                    foreach ($entry['css'] as $cssFile) {
                        $tags[] = sprintf('<link rel="stylesheet" href="/build/%s">', ltrim((string) $cssFile, '/'));
                    }
                }

                if (isset($entry['file'])) {
                    $tags[] = sprintf(
                        '<script type="module" src="/build/%s"></script>',
                        ltrim((string) $entry['file'], '/')
                    );
                }

                return implode(PHP_EOL, $tags);
            }
        }

        return sprintf(
            '<script type="module" src="%s/%s"></script>',
            self::DEV_SERVER_URL,
            ltrim($entryPoint, '/')
        );
    }
}
