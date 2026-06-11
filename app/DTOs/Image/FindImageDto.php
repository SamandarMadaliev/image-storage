<?php

namespace App\DTOs\Image;

readonly class FindImageDto
{
    public function __construct(
        public int $userId,
        public int $imageId,
    ) {}
}
