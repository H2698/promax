<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::gd();
    }

    /**
     * Resize (never upscale), re-encode as webp, and store the upload on the "uploads" disk.
     * Returns the stored relative path.
     */
    public function store(UploadedFile $file, string $directory, int $maxWidth = 1600): string
    {
        $path = trim($directory, '/').'/'.Str::uuid().'.webp';

        $image = $this->manager->read($file->getRealPath());
        $image->scaleDown(width: $maxWidth);

        Storage::disk('uploads')->put($path, (string) $image->toWebp(85));

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('uploads')->delete($path);
        }
    }
}
