<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class ImageStorageRepository
{
    private const DISK = 's3';

    public function store(string $path, string $contents): void
    {
        Storage::disk(self::DISK)->put($path, $contents);
    }

    public function get(string $path): string
    {
        return Storage::disk(self::DISK)->get($path);
    }

    public function delete(string $path): void
    {
        Storage::disk(self::DISK)->delete($path);
    }
}
