<?php

declare(strict_types=1);

namespace App\Classes\Images;

final readonly class ResponsiveImageService
{
    private const DIRECTORY_PERMISSIONS = 0755;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private array $config,
    ) {
    }

    /**
     * @return array{src: string, srcset: string, webp_srcset: string, sizes: string, width: int, height: int}
     */
    public function make(string $sourcePath, string $preset): array
    {
        if ($sourcePath === '' || !isset($this->config['presets'][$preset])) {
            return $this->fallback($sourcePath);
        }

        $absoluteSourcePath = $this->absoluteSourcePath($sourcePath);

        if ($absoluteSourcePath === null || !is_file($absoluteSourcePath)) {
            return $this->fallback($sourcePath);
        }

        try {
            $presetConfig = $this->config['presets'][$preset];
            $jpgSources = [];
            $webpSources = [];

            foreach ($presetConfig['sizes'] as $size) {
                $jpgVariant = $this->generateVariant($absoluteSourcePath, $sourcePath, $preset, $size['width'], $size['height'], 'jpg');
                $webpVariant = $this->generateVariant($absoluteSourcePath, $sourcePath, $preset, $size['width'], $size['height'], 'webp');

                $jpgSources[] = sprintf('%s %dw', $jpgVariant['url'], $size['width']);
                $webpSources[] = sprintf('%s %dw', $webpVariant['url'], $size['width']);
            }

            $largestSize = $presetConfig['sizes'][array_key_last($presetConfig['sizes'])];

            return [
                'src' => $this->publicVariantUrl($sourcePath, $preset, $largestSize['width'], $largestSize['height'], 'jpg'),
                'srcset' => implode(', ', $jpgSources),
                'webp_srcset' => implode(', ', $webpSources),
                'sizes' => (string) $presetConfig['sizes_attribute'],
                'width' => (int) $largestSize['width'],
                'height' => (int) $largestSize['height'],
            ];
        } catch (\Throwable) {
            return $this->fallback($sourcePath);
        }
    }

    /**
     * @return array{src: string, srcset: string, webp_srcset: string, sizes: string, width: int, height: int}
     */
    private function fallback(string $sourcePath): array
    {
        return [
            'src' => $sourcePath,
            'srcset' => '',
            'webp_srcset' => '',
            'sizes' => '100vw',
            'width' => 0,
            'height' => 0,
        ];
    }

    private function absoluteSourcePath(string $sourcePath): ?string
    {
        $publicRoot = realpath(dirname(__DIR__, 3) . '/public');

        if ($publicRoot === false) {
            return null;
        }

        $candidatePath = $publicRoot . '/' . ltrim($sourcePath, '/');
        $resolvedPath = realpath($candidatePath);

        if ($resolvedPath === false) {
            return $candidatePath;
        }

        $publicPrefix = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return str_starts_with($resolvedPath, $publicPrefix) ? $resolvedPath : null;
    }

    /**
     * @return array{path: string, url: string}
     */
    private function generateVariant(
        string $absoluteSourcePath,
        string $publicSourcePath,
        string $preset,
        int $width,
        int $height,
        string $extension,
    ): array {
        $destinationPath = $this->variantPath($publicSourcePath, $preset, $width, $height, $extension);
        $destinationUrl = $this->publicVariantUrl($publicSourcePath, $preset, $width, $height, $extension);

        if (!is_file($destinationPath) || filemtime($destinationPath) < filemtime($absoluteSourcePath)) {
            $this->ensureDirectory(dirname($destinationPath));
            $this->resize($absoluteSourcePath, $destinationPath, $width, $height, $extension);
        }

        return [
            'path' => $destinationPath,
            'url' => $destinationUrl,
        ];
    }

    private function variantPath(string $publicSourcePath, string $preset, int $width, int $height, string $extension): string
    {
        $hash = md5($publicSourcePath);

        return rtrim((string) $this->config['cache_directory'], '/') . sprintf(
            '/%s/%s-%dx%d.%s',
            $preset,
            $hash,
            $width,
            $height,
            $extension,
        );
    }

    private function publicVariantUrl(string $publicSourcePath, string $preset, int $width, int $height, string $extension): string
    {
        $hash = md5($publicSourcePath);

        return rtrim((string) $this->config['cache_url_prefix'], '/') . sprintf(
            '/%s/%s-%dx%d.%s',
            $preset,
            $hash,
            $width,
            $height,
            $extension,
        );
    }

    private function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, self::DIRECTORY_PERMISSIONS, true);
        }
    }

    private function resize(string $sourcePath, string $destinationPath, int $width, int $height, string $extension): void
    {
        $quality = $extension === 'webp' ? 82 : 85;
        $command = sprintf(
            'magick %s -auto-orient -thumbnail %dx%d^ -gravity center -extent %dx%d -strip -quality %d %s',
            escapeshellarg($sourcePath),
            $width,
            $height,
            $width,
            $height,
            $quality,
            escapeshellarg($destinationPath),
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !is_file($destinationPath)) {
            throw new \RuntimeException(sprintf('Unable to generate image variant for %s.', $sourcePath));
        }
    }
}
