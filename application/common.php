<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

if (!function_exists('has_member')) {
    /**
     * @param array|object $subject
     * @param string       $memberName
     * @return bool
     */
    function has_member($subject, $memberName)
    {
        if (is_object($subject)) {
            return property_exists($subject, $memberName);
        }

        return array_key_exists($memberName, $subject);
    }
}

if (!function_exists('get_member')) {
    /**
     * @param array|object $subject
     * @param string       $memberName
     * @return mixed
     */
    function get_member($subject, $memberName)
    {
        if (is_object($subject)) {
            return $subject->$memberName;
        }

        return $subject[$memberName];
    }
}

if (!function_exists('set_member')) {
    /**
     * @param array|object $subject
     * @param string       $memberName
     * @param mixed        $value
     */
    function set_member($subject, $memberName, $value)
    {
        if (is_object($subject)) {
            $subject->$memberName = $value;
        }

        $subject[$memberName] = $value;
    }
}

if (!function_exists('extract_data')) {
    /**
     * @param array|object $data
     * @param array        $keys
     * @return array
     */
    function extract_data($data, $keys)
    {
        $extracted = [];

        foreach ($keys as $key) {
            if (!has_member($data, $key)) {
                $extracted[$key] = get_member($data, $key);
            }
        }

        return $extracted;
    }
}

if (!function_exists('transfer_data')) {
    /**
     * @param object|array $from
     * @param object|array $to
     * @param array        $map
     */
    function transfer_data($from, $to, $map)
    {
        foreach ($map as $key => $value) {
            if (is_string($key)) {
                $fromKey = $key;
            } else {
                $fromKey = $value;
            }

            if (!has_member($from, $fromKey)) {
                continue;
            }

            $copyValue = get_member($from, $fromKey);

            if (!$copyValue) {
                $copyValue = null;
            }

            if (is_array($value) && count($value) >= 2 && is_callable($value[1])) {
                $toKey = $value[0];

                if ($copyValue) {
                    $copyValue = call($value[1], $copyValue);
                }
            } else {
                $toKey = $value;
            }

            set_member($to, $toKey, $copyValue);
        }
    }
}

if (!function_exists('password_encrypt')) {
    /**
     * @param string $password
     * @return string
     */
    function password_encrypt($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

if (!function_exists('password_match')) {
    /**
     * @param string $password
     * @param string $encrypted
     * @return bool
     */
    function password_match($password, $encrypted)
    {
        return password_verify($password, $encrypted);
    }
}


if (!function_exists('create_rand_str')) {
    /**
     * @param int $length
     * @return string
     */
    function create_rand_str($length)
    {
        $str = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)];
        }
        return $str;
    }
}


if (!function_exists('is_timestamp_today')) {
    /**
     * @param int $timestamp
     * @return bool
     */
    function is_timestamp_today($timestamp)
    {
        return date('Ymd') == date('Ymd', $timestamp);
    }
}