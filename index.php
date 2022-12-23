<?php

//echo ini_get("request_order");exit;

require '../vendor/autoload.php';

const URL = 'http://applicant-test.us-east-1.elasticbeanstalk.com';

$result = webScrap('http://applicant-test.us-east-1.elasticbeanstalk.com');

postWithCurl($result['cookie'], $result['token']);

exit;

function postWithGuzzle($cookie = '', $token = '')
{
    $client = new \GuzzleHttp\Client();

    $response = $client->post(
        URL, [
            'form_params' => [
                'token' => $token
            ],
            'headers' => makeHeaders($cookie),
            'debug' => false
        ]
    );

    echo $response->getBody();
}

/**
 * @param string $url - the url you wish to fetch.
 * @return string - the raw html respose. 
 */
function getToken($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $response = curl_exec($ch); 
    curl_close($ch); 
    
    $DOM = new DOMDocument;
    $DOM->loadHTML($response);

    $token = $DOM->getElementById('token')->getAttribute('value');

    $token = encryptToken($token);

    return trim($token);
}

/**
 *
 * @param string $url -  the url you wish to fetch.
 * @return array - the http headers returned 
 */
function webScrap($url) 
{
    $headers = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
        function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );
    
    $response = curl_exec($ch);
    
    $cookie = $headers['set-cookie'][0];

    $cookie = explode('=', $cookie);

    $cookie = explode(';', $cookie[1]);

    $cookie = trim($cookie[0]);

    $DOM = new DOMDocument;
    $DOM->loadHTML($response);

    $token = $DOM->getElementById('token')->getAttribute('value');

    $token = encryptToken($token);

    return [
        'cookie' => $cookie,
        'token' => $token
    ];
}



function postWithCurl($cookie = '', $token = '')
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'token=' . $token);
    curl_setopt($ch, CURLOPT_ENCODING, 'deflate, gzip, br');

    $headers = array();
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
    $headers[] = 'Accept-Language: en-US,en;q=0.9,pt;q=0.8';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'Cookie: PHPSESSID=' . $cookie;
    $headers[] = 'Origin: ' . URL;
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Referer: ' . URL . '/';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);

    var_export($result);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    
    curl_close($ch);

    return $result;
}

function encryptToken(?string $token) : string
{
    $replacements = [
        'a'=> 'z',
        'b'=> 'y',
        'c'=> 'x',
        'd'=> 'w',
        'e'=> 'v',
        'f'=> 'u',
        'g'=> 't',
        'h'=> 's',
        'i'=> 'r',
        'j'=> 'q',
        'k'=> 'p',
        'l'=> 'o',
        'm'=> 'n',
        'n'=> 'm',
        'o'=> 'l',
        'p'=> 'k',
        'q'=> 'j',
        'r'=> 'i',
        's'=> 'h',
        't'=> 'g',
        'u'=> 'f',
        'v'=> 'e',
        'w'=> 'd',
        'x'=> 'c',
        'y'=> 'b',
        'z'=> 'a',
        '0'=> '9',
        '1'=> '8',
        '2'=> '7',
        '3'=> '6',
        '4'=> '5',
        '5'=> '4',
        '6'=> '3',
        '7'=> '2',
        '8'=> '1',
        '9'=> '0'
    ];

    for($i = 0; $i < strlen($token); $i++) {
        $token[$i] = $replacements[$token[$i]] ?? $token[$i];
    }

    return $token;
}

function makeHeaders(string $cookie) : array
{
    $headers = array();
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
    $headers[] = 'Accept-Language: en-US,en;q=0.9,pt;q=0.8';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'Cookie: PHPSESSID=' . $cookie;
    $headers[] = 'Origin: ' . URL;
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Referer: ' . URL . '/';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';

    return $headers;
}
