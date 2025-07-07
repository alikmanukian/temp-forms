<?php

namespace App\Http\Controllers;

use App\Events\ImagePlaceholderGeneratedEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use RuntimeException;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;

class ImageTransformationController extends Controller
{
    public function __invoke(string $image, string $extension)
    {
        $cacheKey = 'image-placeholder:'.$image.':'.filemtime($path = public_path("images/$image.$extension"));

        if (file_exists($path) === false) {
            abort(404, 'Image not found');
        }

        $base64 = Cache::rememberForever($cacheKey, function () use ($path) {
            $tempPath = sys_get_temp_dir() . '/' . uniqid('placeholder_', true) .'.'.File::extension($path);

            // Ensure the Image package is installed and available
            $this->saveTemporaryImage($path, $tempPath);

            event(new ImagePlaceholderGeneratedEvent($tempPath));

            $data = base64_encode(file_get_contents($tempPath));

            @unlink($tempPath);

            return $data;
        });

        return response()->stream(function () use ($base64) {
            echo base64_decode($base64);
        }, 200, [
            'Content-Type' => File::mimeType($path),
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /**
     * @throws CouldNotLoadImage
     */
    private function saveTemporaryImage(string $path, string $tempPath): void
    {
        $width = 40;
        $blur = 30;
        $quality = 100;

        if (file_exists($imageMagickPath = config('app.imagemagick.path'))) {
            $command = sprintf(
                '%s %s -resize x%d -blur 0x%s -quality %d %s',
                escapeshellarg($imageMagickPath),
                escapeshellarg($path),
                $width,
                $blur/10,
                $quality,
                escapeshellarg($tempPath)
            );

            exec($command, $output, $status);

            if ($status !== 0) {
                throw new RuntimeException('ImageMagick failed: '.implode("\n", $output));
            }

        } else {
            Image::load($path)
                ->width($width)
                ->blur($blur)
                ->quality($quality)
                ->save($tempPath);
        }
    }
}
