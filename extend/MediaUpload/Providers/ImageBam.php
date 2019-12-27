<?php

namespace MediaUpload\Providers;

use Curl\Curl;
use Exception;
use PHPHtmlParser\Dom;
use think\facade\Log;

class ImageBam implements ImageUploadProvider
{
    /**
     * @param string $imagePath
     * @param bool   $gif
     * @return false|array
     * @throws Exception
     */
    public static function upload($imagePath, $gif = false)
    {
        $curl = new Curl();

        $curl->setReferer('http://www.imagebam.com/basic-upload');

        $curl->post('http://www.imagebam.com/sys/upload/save', [
            'file[0]' => new \CURLFile($imagePath),
            'content_type' => '1',
            'thumb_size' => '350',
            'thumb_aspect_ratio' => 'resize',
            'thumb_file_type' => $gif ? 'gif' : 'jpg',
        ]);

        if ($curl->error) {
            return false;
        }

        $dom = new Dom();
        $dom->load($curl->response);
        $errorBoxes = $dom->find('.box_error');

        if ($errorBoxes->count()) {
            /**
             * @var Dom\AbstractNode $errorBox
             */
            $errorBox = $errorBoxes[0];
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => $errorBox]);
            return false;
        }

        $inputs = $dom->find('.dlinput_container .dlinput');

        if (!$inputs->count()) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => 'Image result not found. ".dlinput"']);
            return false;
        }

        /**
         * @var Dom\AbstractNode $input
         */
        $input = $inputs[0];
        $linkHTML = $input->getAttribute('value');

        preg_match('/href="(.*?)"/', $linkHTML, $matches);

        if (count($matches) < 2) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => 'Image outLink not found. ".dlinput value > a.href"']);
            return false;
        }

        $outLink = $matches[1];

        preg_match('/src="(.*?)"/', $linkHTML, $matches);

        if (count($matches) < 2) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => 'Thumb image not found. ".dlinput value > img.src"']);
            return false;
        }

        $thumbLink = $matches[1];

        $curl = new Curl();

        $curl->get($outLink);

        if ($curl->error) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => 'Image outLink unreachable']);
            return false;
        }

        $dom->load($curl->response);

        $images = $dom->find('meta[property="og:image"]');

        if (!$images->count()) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImageBam', 'message' => 'Image outLink: no image found']);
            return false;
        }

        /**
         * @var Dom\AbstractNode $fullImage
         */
        $fullImage = $images[0];
        $fullImageLink = $fullImage->getAttribute('content');

        return [
            'thumbnail_image' => $thumbLink,
            'original_image' => $fullImageLink,
            'page_link' => $outLink,
        ];
    }
}