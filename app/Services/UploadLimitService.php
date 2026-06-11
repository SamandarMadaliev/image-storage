<?php

namespace App\Services;

use App\DTOs\Image\DailyUploadLimitDto;
use App\Exceptions\UploadLimitExceededException;
use App\Repositories\DailyUploadLimitRepository;

class UploadLimitService
{
    public function __construct(
        private DailyUploadLimitRepository $dailyUploadLimitRepository,
    ) {}

    public function reserveUploadSlot(int $userId): void
    {
        $dto = new DailyUploadLimitDto($userId);
        $count = $this->dailyUploadLimitRepository->increment($dto);

        if ($count > $this->dailyLimit()) {
            $this->dailyUploadLimitRepository->decrement($dto);

            throw new UploadLimitExceededException($this->dailyLimit());
        }
    }

    public function releaseUploadSlot(int $userId): void
    {
        $this->dailyUploadLimitRepository->decrement(new DailyUploadLimitDto($userId));
    }

    private function dailyLimit(): int
    {
        return (int) config('images.daily_upload_limit');
    }
}
