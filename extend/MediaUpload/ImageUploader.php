<?php


namespace MediaUpload;

use Exception;
use MediaUpload\Providers\ImageBam;
use MediaUpload\Providers\ImgBox;
use think\File;

class ImageUploader
{
    public static $providers = [
        ImageBam::class,
        ImgBox::class,
    ];

    /**
     * @param File $image
     * @param bool $gif
     * @return array|false
     * @throws Exception
     */
    public static function upload(File $image, $gif = false)
    {
        $image = $image->rule('md5')->move('../runtime/temp/');
        $imagePath = $image->getRealPath();

        $result = false;

        foreach (static::$providers as $provider) {
            $result = call_user_func([$provider, 'upload'], $imagePath, $gif);

            if ($result) {
                break;
            }
        }

        unlink($imagePath);

        return $result;
    }
}
