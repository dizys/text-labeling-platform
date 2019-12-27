<?php


namespace MediaUpload\Providers;


interface ImageUploadProvider
{
    /**
     * @param      $imagePath
     * @param bool $gif
     * @return array|false
     */
    public static function upload($imagePath, $gif = false);
}