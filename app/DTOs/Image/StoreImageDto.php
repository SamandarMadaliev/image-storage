<?php

namespace App\DTOs\Image;

readonly class StoreImageDto
{
    public function __construct(
        public int $userId,
        public string $originalName,
        public string $filePath,
        public string $extension,
        public int $size,
    ) {}
}
