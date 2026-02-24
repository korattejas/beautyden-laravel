<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use SpacesAPI\Spaces;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class ImageUploadHelper
{
    public static function imageUpload($files): string
    {
        $image_path = 'uploads/' . date('Y') . '/' . date('m');
        if (!File::exists(public_path() . "/" . $image_path)) {
            File::makeDirectory(public_path() . "/" . $image_path, 0777, true);
        }
        $extension = $files->getClientOriginalExtension();
        $destination_path = public_path() . '/' . $image_path;
        $file_name = uniqid() . '.' . $extension;
        $files->move($destination_path, $file_name);
        return $image_path . '/' . $file_name;
    }

    public static function serviceCategoryimageUpload($files): string
    {
        $image_path = 'uploads/service-category';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function serviceSubcategoryImageUpload($files): string
    {
        $image_path = 'uploads/service-subcategory';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function ProductBrandImageUpload($files): string
    {
        $image_path = 'uploads/product-brand';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

     public static function PortfolioImageUpload($files): string
    {
        $image_path = 'uploads/portfolio';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function blogCategoryimageUpload($files): string
    {
        $image_path = 'uploads/blog-category';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function blogsimageUpload($files): string
    {
        $image_path = 'uploads/blogs';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function serviceimageUpload($files): string
    {
        $image_path = 'uploads/service';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function teamMemberimageUpload($files): string
    {
        $image_path = 'uploads/team-member';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function reviewCustomerImageUpload($files): string
    {
        $image_path = 'uploads/review/customer-photos';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function reviewImageUpload($files): string
    {
        $image_path = 'uploads/review/photos';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function reviewVideoUpload($file): string
    {
        $video_path = 'uploads/review/videos';

        if (!File::exists(public_path($video_path))) {
            File::makeDirectory(public_path($video_path), 0777, true, true);
        }

        $extension = $file->getClientOriginalExtension();

        $file_name = uniqid('review_video_') . '.' . $extension;

        $file->move(public_path($video_path), $file_name);

        return $file_name;
    }

    public static function homeCounterImageUpload($files): string
    {
        $image_path = 'uploads/home-counters';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }

    public static function cityimageUpload($files): string
    {
        $image_path = 'uploads/city';
        if (!File::exists(public_path($image_path))) {
            File::makeDirectory(public_path($image_path), 0777, true);
        }

        $extension = $files->getClientOriginalExtension();
        $file_name = uniqid() . '.' . $extension;

        $files->move(public_path($image_path), $file_name);

        return $file_name;
    }












    public static function uploadCategoryImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/categories/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadFlashScreenImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/flashScreen/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadSubcategoryImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/subCategories/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadPoseImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/posesImages/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadPhotographerPortfolioImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/photographerPortfolio/images/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadPhotographerPortfolioVideoS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/photographerPortfolio/videos/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    public static function uploadPhotographerProfileImageS3(UploadedFile $file): string
    {
        try {
            $filePath = "public/photographerProfile/" . self::generateUniqueFileName($file);
            return self::uploadToS3($file, $filePath);
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    private static function uploadToS3(UploadedFile $file, string $filePath): string
    {
        try {
            // $uploadSuccess = Storage::disk('s3')->put($filePath, file_get_contents($file));
            $uploadSuccess = Storage::disk('s3')->put($filePath, file_get_contents($file), [
                'ServerSideEncryption' => 'AES256'
            ]);

            if (!$uploadSuccess) {
                throw new \Exception('Failed to upload image to S3');
            }

            $mainUrl = Storage::disk('s3')->url($filePath);
            $parsedUrl = parse_url($mainUrl);
            return ltrim($parsedUrl['path'], '/');
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    private static function deleteFromS3(string $filePath): bool
    {
        try {
            if (Storage::disk('s3')->exists($filePath)) {
                return Storage::disk('s3')->delete($filePath);
            }

            return false;
        } catch (\Exception $e) {
            logger()->error("S3 Upload Error: " . $e->getMessage());
            throw new \Exception('Failed to upload image to S3: ' . $e->getMessage());
        }
    }

    private static function generateUniqueFileName(UploadedFile $file): string
    {
        return date('Y') . '-' . date('m') . '-' . date('d') . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    public static function s3ImageUpload(UploadedFile $file): string
    {
        $imagePath = date('Y') . '/' . date('m');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $imagePath . '/' . $fileName;
        $uploadSuccess = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
        if (!$uploadSuccess) {
            throw new \Exception('Failed to upload image to S3');
        }
        return Storage::disk('s3')->url($filePath);
    }

    public static function logoUpload($files): string
    {
        $image_path = 'branding/img/' . date('Y') . '/' . date('m');
        if (!File::exists(public_path() . "/" . $image_path)) {
            File::makeDirectory(public_path() . "/" . $image_path, 0777, true);
        }
        $extension = $files->getClientOriginalExtension();
        $destination_path = public_path() . '/' . $image_path;
        $file_name = uniqid() . '.' . $extension;
        $files->move($destination_path, $file_name);
        return $image_path . '/' . $file_name;
    }
}
