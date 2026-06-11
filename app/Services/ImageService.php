<?php

namespace App\Services;

use App\DTOs\Image\FindImageDto;
use App\DTOs\Image\ImageContentDto;
use App\DTOs\Image\StoreImageDto;
use App\DTOs\Image\UploadImageDto;
use App\Models\Image;
use App\Models\User;
use App\Repositories\ImageRepository;
use App\Repositories\ImageStorageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ImageService
{
    public function __construct(
        private ImageRepository $imageRepository,
        private ImageStorageRepository $storageRepository,
        private UploadLimitService $uploadLimitService,
    ) {}

    public function store(UploadImageDto $dto): Image
    {
        $this->uploadLimitService->reserveUploadSlot($dto->userId);

        try {
            $extension = strtolower($dto->file->getClientOriginalExtension());
            $filePath = $dto->userId.'/'.Str::uuid()->toString().'.'.$extension;

            $this->storageRepository->store(
                $filePath,
                $dto->file->get(),
            );

            return $this->imageRepository->create(new StoreImageDto(
                userId: $dto->userId,
                originalName: $dto->file->getClientOriginalName(),
                filePath: $filePath,
                extension: $extension,
                size: $dto->file->getSize(),
            ));
        } catch (Throwable $exception) {
            $this->uploadLimitService->releaseUploadSlot($dto->userId);

            throw $exception;
        }
    }

    /**
     * @return Collection<int, Image>
     */
    public function index(User $user): Collection
    {
        return $this->imageRepository->findAllByUserId($user->id);
    }

    public function show(FindImageDto $dto): ImageContentDto
    {
        $image = $this->findUserImage($dto);

        return new ImageContentDto(
            image: $image,
            contents: $this->storageRepository->get($image->file_path),
        );
    }

    public function destroy(FindImageDto $dto): void
    {
        $image = $this->findUserImage($dto);

        $this->storageRepository->delete($image->file_path);
        $this->imageRepository->delete($image);
    }

    private function findUserImage(FindImageDto $dto): Image
    {
        $image = $this->imageRepository->findByIdForUser($dto->imageId, $dto->userId);

        if ($image === null) {
            throw new NotFoundHttpException('Image not found.');
        }

        return $image;
    }
}
