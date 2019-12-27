<?php

use think\response\Json;

if (!function_exists('success')) {
    /**
     * @param null   $data
     * @param string $message
     * @param int    $code
     * @return Json
     */
    function success($data = null, $message = 'OK', $code = 200)
    {
        $result = [
            'code' => $code,
            'message' => $message,
        ];

        if ($data) {
            $result['data'] = $data;
        }

        return json($result);
    }
}

if (!function_exists('failure')) {
    /**
     * @param string $message
     * @param int    $code
     * @param null   $data
     * @return Json
     */
    function failure($message = 'Failed', $code = 500, $data = null)
    {
        $result = [
            'code' => $code,
            'message' => $message,
        ];

        if ($data) {
            $result['data'] = $data;
        }

        return json($result);
    }
}