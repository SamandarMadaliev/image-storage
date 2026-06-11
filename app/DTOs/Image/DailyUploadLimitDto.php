<?php

namespace App\DTOs\Image;

readonly class DailyUploadLimitDto
{
    public function __construct(
        public int $userId,
    ) {}
}
