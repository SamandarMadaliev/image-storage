<?php

namespace App\Repositories;

use App\DTOs\Image\DailyUploadLimitDto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class DailyUploadLimitRepository
{
    public function increment(DailyUploadLimitDto $dto): int
    {
        $key = $this->key($dto);
        $count = (int) Redis::incr($key);

        if ($count === 1) {
            Redis::expire($key, $this->secondsUntilEndOfDay());
        }

        return $count;
    }

    public function decrement(DailyUploadLimitDto $dto): int
    {
        $count = (int) Redis::decr($this->key($dto));

        return max($count, 0);
    }

    private function key(DailyUploadLimitDto $dto): string
    {
        $date = Carbon::now()->toDateString();

        return "user:{$dto->userId}:uploads:{$date}";
    }

    private function secondsUntilEndOfDay(): int
    {
        return (int) Carbon::now()->diffInSeconds(Carbon::now()->endOfDay());
    }
}
