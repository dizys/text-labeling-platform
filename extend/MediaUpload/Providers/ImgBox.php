<?php


namespace MediaUpload\Providers;


use Curl\Curl;
use think\facade\Log;

class ImgBox implements ImageUploadProvider
{
    public static function upload($imagePath, $gif = false)
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);

        $curl->get('https://imgbox.com/ajax/token/generate');

        $response = $curl->response;

        if (is_string($response)) {
            $response = json_decode($response);
        }

        if ($curl->error || !is_object($response) || !property_exists($response, 'token_id') || !property_exists($response, 'token_secret')) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImgBox', 'message' => 'Token generation api not reachable']);
            return false;
        }

        $tokenId = $response->token_id;
        $tokenSecret = $response->token_secret;

        $curl->post('https://imgbox.com/upload/process', [
            'token_id' => $tokenId,
            'token_secret' => $tokenSecret,
            'content_type' => '1',
            'thumbnail_size' => '350r',
            'gallery_id' => null,
            'gallery_secret' => null,
            'comments_enabled' => '0',
            'files[0]' => new \CURLFile($imagePath),
        ]);

        if ($curl->error) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImgBox', 'message' => 'Image upload api not reachable']);
            return false;
        }

        $response = $curl->response;

        if (is_string($response)) {
            $response = json_decode($response);
        }

        if (!is_object($response) || !property_exists($response, 'files') || count($response->files) < 1) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImgBox', 'message' => 'Result file list not available']);
            return false;
        }

        $file = $response->files[0];

        if (!property_exists($file, 'url') || !property_exists($file, 'original_url') || !property_exists($file, 'thumbnail_url')) {
            Log::error(['tag' => 'Image upload failed', 'host' => 'ImgBox', 'message' => 'Urls not available']);
            return false;
        }

        $thumbLink = $file->thumbnail_url;
        $fullImageLink = $file->original_url;
        $outLink = $file->url;

        return [
            'thumbnail_image' => $thumbLink,
            'original_image' => $fullImageLink,
            'page_link' => $outLink,
        ];
    }
}