<?php

namespace App\Bll;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ImageUploader
{
    protected $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function upload(UploadedFile $file)
    {
        try {
            // Create directories if they don't exist
            File::makeDirectory($this->basePath, 0777, true, true);

            // Resize and save the original image
            Image::make($file)->save($this->basePath . 'web.' . $file->getClientOriginalExtension());
            // Create and save the thumbnail

            return 'web.' . $file->getClientOriginalExtension();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function moveUploadedFile($fileName, $destinationPath, $allowMobile = false)
    {
        $getFile = public_path('temp/' . $fileName);
        if (!file_exists($getFile)) {
            return false;
        }
        File::makeDirectory($this->basePath, 0777, true, true);
        if ($allowMobile) {
            // Get the file extension
            $fileExtension = pathinfo($getFile, PATHINFO_EXTENSION);
            Image::make($getFile)->save($this->basePath . 'web.' . $fileExtension);

            unlink($getFile); // Delete the original file after processing
            return 'web.' . pathinfo($getFile, PATHINFO_EXTENSION);
        } else {
            // Move the file to the destination path
            rename($getFile, $destinationPath . $fileName);
            return $fileName;
        }
    }

}
