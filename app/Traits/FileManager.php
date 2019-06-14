<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileManager
{
    public function putFile($file, $name = null,$folder = null) {

        $path = $folder ?? str_random(64);
        $path = 'images'.'/'.$path;

        if ($file instanceof UploadedFile) {
            $returned_path = Storage::putFileAs($path, $file, $name, ["visibility" => "public"]);
            return env('DO_SPACES_ENDPOINT').'/'.env('DO_SPACES_BUCKET').'/'.$returned_path;
        } else if ($file instanceof \Intervention\Image\Image) {

            if (empty($name))
                throw new \Exception("Unsupported name for Image.");

            $rel = $path.'/'.$name;

            Storage::put($rel, $file->stream()->__toString(),["visibility" => "public"]);

            return url('/storage/'.$rel);
        }

        throw new \Exception("Unsupported object type file");
    }

    protected function clean_without_dots($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9_\-]/', '', $string); // Removes special chars.
    }

    public function resizeImage($file,$thumbnail = null) {
        $image = Image::make($file);

        $height = $image->height();
        $width = $image->width();

        if($thumbnail){
            $maximum_image_size = env('MAXIMUM_THUMBNAIL_SIZE', 300);
        }else{
            $maximum_image_size = env('MAXIMUM_IMAGE_SIZE', 1024);
        }

        if ($height >= $width) {
            if ($height > $maximum_image_size) {
                $image = $image->resize(null, $maximum_image_size, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
        } else {
            if ($width > $maximum_image_size) {
                $image = $image->resize($maximum_image_size, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
        }
        return $image;
    }

    public function uploadFile(UploadedFile $file){
        $original_name = $file->getClientOriginalName();
        $filename      = $this->clean_without_dots(pathinfo($original_name, PATHINFO_FILENAME));
        $extension     = pathinfo($original_name, PATHINFO_EXTENSION);
        $name          = "$filename.$extension";
        $mime_type     = $file->getMimeType();

        try {
            if(substr($mime_type, 0, 5) == 'image'){
                $file = $this->resizeImage($file);
            }
            $path = $this->putFile($file, $name);
        }catch (\Exception $exception){
            throw new $exception;
        }

        return $path;
    }

}
