<?php

namespace Tests\Unit;

use App\DTOs\Image\DailyUploadLimitDto;
use App\Exceptions\UploadLimitExceededException;
use App\Repositories\DailyUploadLimitRepository;
use App\Services\UploadLimitService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UploadLimitServiceTest extends TestCase
{
    #[Test]
    public function it_reserves_an_upload_slot_when_under_daily_limit(): void
    {
        config(['images.daily_upload_limit' => 100_000]);

        $repository = $this->createMock(DailyUploadLimitRepository::class);
        $repository->expects($this->once())
            ->method('increment')
            ->with($this->callback(fn (DailyUploadLimitDto $dto) => $dto->userId === 1))
            ->willReturn(1);

        $repository->expects($this->never())->method('decrement');

        $service = new UploadLimitService($repository);

        $service->reserveUploadSlot(1);
    }

    #[Test]
    public function it_throws_when_daily_upload_limit_is_exceeded(): void
    {
        config(['images.daily_upload_limit' => 2]);

        $repository = $this->createMock(DailyUploadLimitRepository::class);
        $repository->expects($this->once())
            ->method('increment')
            ->willReturn(3);
        $repository->expects($this->once())
            ->method('decrement')
            ->with($this->callback(fn (DailyUploadLimitDto $dto) => $dto->userId === 1));

        $service = new UploadLimitService($repository);

        $this->expectException(UploadLimitExceededException::class);

        $service->reserveUploadSlot(1);
    }

    #[Test]
    public function it_releases_a_reserved_upload_slot(): void
    {
        $repository = $this->createMock(DailyUploadLimitRepository::class);
        $repository->expects($this->once())
            ->method('decrement')
            ->with($this->callback(fn (DailyUploadLimitDto $dto) => $dto->userId === 5));

        $service = new UploadLimitService($repository);

        $service->releaseUploadSlot(5);
    }
}
