<?php

namespace App\DTOs\Image;

use Illuminate\Http\UploadedFile;

readonly class UploadImageDto
{
    public function __construct(
        public int $userId,
        public UploadedFile $file,
    ) {}
}
