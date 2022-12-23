<?php

namespace Src;

use DOMDocument;
use Goutte\Client;

final class Request
{
    protected Curl $curlService;
    
    public function __construct()
    {
        $this->curlService = new Curl();
    }
    
    public function getDataFromUrl(string $url) : array
    {
        $data = $this->makeCrawlerRequest($url);

        $data['token'] = Token::encrypt($data['token']);

        return $data;
    }

    function post($cookie, $token, $url) : ?string
    {
        return $this->curlService->post($cookie, $token, $url);
    }

    public function getAnswer(string $html) : ?string
    {
        $DOM = new DOMDocument();
        $DOM->loadHTML($html);

        return $DOM->getElementById('answer')?->textContent;
    }

    private function makeCrawlerRequest(string $url) : array
    {
        $client = new Client();

        $crawler = $client->request('GET', $url);

        $token = null;
        
        $crawler->filter('input')->each(function ($node) use (&$token) {
            if ($node->attr('type') === 'hidden') {
                $token = $node->attr('value');
            }
        });

        $cookie = $client->getCookieJar()->get('PHPSESSID')->getValue();

        return [
            'cookie' => $cookie,
            'token' => $token
        ];
    }
}
