<?php

namespace App\DTOs\Image;

use App\Models\Image;

readonly class ImageContentDto
{
    public function __construct(
        public Image $image,
        public string $contents,
    ) {}
}
