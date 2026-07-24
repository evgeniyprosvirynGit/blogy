<?php

declare(strict_types=1);

namespace App\Core;

final class Vite
{
    public static function tags(array $config, string $basePath): string
    {
        $entryPoint = (string) ($config['entrypoint'] ?? '');
        $manifestPath = $basePath . (string) ($config['manifest_path'] ?? '');
        $buildDirectory = rtrim((string) ($config['build_directory'] ?? '/build'), '/');
        $devServerUrl = rtrim((string) ($config['dev_server_url'] ?? ''), '/');

        if (is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            $entry = $manifest[$entryPoint] ?? null;

            if (is_array($entry)) {
                $tags = [];

                if (isset($entry['css']) && is_array($entry['css'])) {
                    foreach ($entry['css'] as $cssFile) {
                        $tags[] = sprintf(
                            '<link rel="stylesheet" href="%s/%s">',
                            $buildDirectory,
                            ltrim((string) $cssFile, '/')
                        );
                    }
                }

                if (isset($entry['file'])) {
                    $tags[] = sprintf(
                        '<script type="module" src="%s/%s"></script>',
                        $buildDirectory,
                        ltrim((string) $entry['file'], '/')
                    );
                }

                return implode(PHP_EOL, $tags);
            }
        }

        return sprintf(
            '<script type="module" src="%s/%s"></script>',
            $devServerUrl,
            ltrim($entryPoint, '/')
        );
    }
}
