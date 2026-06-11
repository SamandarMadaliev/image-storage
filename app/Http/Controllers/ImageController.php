<?php

namespace App\Http\Controllers;

use App\DTOs\Image\FindImageDto;
use App\DTOs\Image\UploadImageDto;
use App\Http\Requests\DestroyImageRequest;
use App\Http\Requests\IndexImageRequest;
use App\Http\Requests\ShowImageRequest;
use App\Http\Requests\StoreImageRequest;
use App\Services\ImageService;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function __construct(
        private ImageService $imageService,
    ) {}

    /**
     * Upload a new image.
     *
     * Only PNG and JPEG files are allowed. Maximum file size is 5MB.
     * Each user can upload up to 100,000 images per day.
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $image = $this->imageService->store(new UploadImageDto(
            userId: $request->user()->id,
            file: $request->file('image'),
        ));

        return response()->json([
            'image' => $image,
        ], Response::HTTP_CREATED);
    }

    /**
     * List all images uploaded by the authenticated user.
     */
    public function index(IndexImageRequest $request): JsonResponse
    {
        $images = $this->imageService->index($request->user());

        return response()->json([
            'images' => $images,
        ]);
    }

    /**
     * Get an image file by record ID.
     *
     * Use the numeric `id` returned from upload or list endpoints — not the original file name.
     */
    #[PathParameter(
        'image',
        description: 'Image record ID from `POST /images` or `GET /images` (e.g. `1`). Not the file name.',
        type: 'int',
        example: 1,
    )]
    public function show(ShowImageRequest $request, string $image): Response
    {
        $result = $this->imageService->show(new FindImageDto(
            userId: $request->user()->id,
            imageId: (int) $image,
        ));

        return response(
            $result->contents,
            Response::HTTP_OK,
            [
                'Content-Type' => self::MIME_TYPES[$result->image->extension] ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$result->image->original_name.'"',
            ],
        );
    }

    /**
     * Delete an image by record ID.
     *
     * Use the numeric `id` returned from upload or list endpoints — not the original file name.
     */
    #[PathParameter(
        'image',
        description: 'Image record ID from `POST /images` or `GET /images` (e.g. `1`). Not the file name.',
        type: 'int',
        example: 1,
    )]
    public function destroy(DestroyImageRequest $request, string $image): JsonResponse
    {
        $this->imageService->destroy(new FindImageDto(
            userId: $request->user()->id,
            imageId: (int) $image,
        ));

        return response()->json([
            'message' => 'Image deleted successfully.',
        ]);
    }
}
