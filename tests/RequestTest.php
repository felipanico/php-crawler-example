<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Src\Curl;
use Src\Enum;
use Src\Request;

require "./vendor/autoload.php";


class RequestTest extends TestCase
{
    public function testGetDataWithSuccess()
    {
        $data = $this->getRequestService()->getDataFromUrl(Enum::URL);
        
        $this->assertTrue(
            $data['cookie'] !== '' && $data['token'] !== ''
        );
    }
    
    public function testGetDataWithError()
    {
        $data = $this->getRequestService()->getDataFromUrl('random-string');

        $this->assertFalse(
            $data['cookie'] !== '' && $data['token'] !== ''
        );
    }

    public function testPostStatusWithAnswer()
    {
        $result = $this->getCurlService()
            ->post('random-cookie', 'random-token', Enum::URL);

        $this->assertTrue($result != '');
    }

    private function getCurlService() : Curl
    {
        return new Curl();
    }

    private function getRequestService() : Request
    {
        return new Request();
    }
}