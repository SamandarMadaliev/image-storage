<?php

namespace App\Repositories;

use App\DTOs\Image\StoreImageDto;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;

class ImageRepository
{
    public function create(StoreImageDto $dto): Image
    {
        return Image::create([
            'user_id' => $dto->userId,
            'original_name' => $dto->originalName,
            'file_path' => $dto->filePath,
            'extension' => $dto->extension,
            'size' => $dto->size,
        ]);
    }

    public function findByIdForUser(int $id, int $userId): ?Image
    {
        return Image::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * @return Collection<int, Image>
     */
    public function findAllByUserId(int $userId): Collection
    {
        return Image::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function delete(Image $image): void
    {
        $image->delete();
    }
}
