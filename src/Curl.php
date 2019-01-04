<?php

namespace Huangdijia\Curl;

use Exception;

class Curl
{
    public function __construct()
    {
        //
    }

    public function request($method = 'POST', $url = '', $data = null)
    {
        if (!function_exists('curl_init')) {
            throw new Exception("Not support curl", 1);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $method == 'POST' ? 1 : 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);

        if (!is_null($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);

        if ($errno) {
            throw new Exception($error, $errno);
        }

        return $response;
    }

    public function post($url = '', $data = null)
    {
        return self::request('POST', $url, $data);
    }

    public function get($url = '', $data = null, $compatSpace = false)
    {
        $query = '';

        if (is_array($data) && !empty($data)) {
            $query = http_build_query($data);
        } elseif (is_string($data)) {
            $query = $data;
        }

        // 兼容+
        if ($compatSpace) {
            $query = str_replace('+', '%20', $query);
        }

        $sep = false === strpos($url, '?') ? '?' : '&';

        if ('' != $query) {
            $url .= $sep . $query;
        }

        return self::request('GET', $url);
    }
}
