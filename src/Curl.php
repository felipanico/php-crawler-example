<?php

namespace Src;

use CurlHandle;

final class Curl
{
    public function init(string $url) : CurlHandle
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    public function post($cookie = '', $token = '', $url = '') : ?string
    {
        $ch = $this->init($url);
        
        $this->makePostOptions($ch, $token, $cookie, $url);

        $result = curl_exec($ch);

        $this->handleError($ch);
        
        return $result;
    }

    private function handleError(CurlHandle $ch) : void
    {
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
    }

    private function makePostOptions(CurlHandle $ch, string $token, string $cookie, string $url) : CurlHandle
    {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'token=' . $token);
        curl_setopt($ch, CURLOPT_ENCODING, 'deflate, gzip, br');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->makeHeaders($cookie, $url));

        return $ch;
    }

    private function makeHeaders(string $cookie, string $url) : array
    {
        $headers = array();
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,pt;q=0.8';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Cookie: PHPSESSID=' . $cookie;
        $headers[] = 'Origin: ' . $url;
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Referer: ' . $url . '/';
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';

        return $headers;
    }
}
