<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public function uploadAvatar(UploadedFile $file): array
    {
        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'reloved/avatars',
            'transformation' => [
                'width' => 400,
                'height' => 400,
                'crop' => 'fill',
                'gravity' => 'face',
            ],
        ]);

        return [
            'cloudinary_public_id' => $uploadedFile->getPublicId(),
            'cloudinary_url' => $uploadedFile->getSecurePath(),
        ];
    }

    public function uploadProductImage(UploadedFile $file, int $order = 0): array
    {
        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'reloved/products',
            'transformation' => [
                'width' => 1200,
                'height' => 1200,
                'crop' => 'limit',
                'quality' => 'auto',
            ],
        ]);

        return [
            'cloudinary_public_id' => $uploadedFile->getPublicId(),
            'cloudinary_url' => $uploadedFile->getSecurePath(),
        ];
    }

    public function deleteImage(string $publicId): void
    {
        Cloudinary::destroy($publicId);
    }
}

