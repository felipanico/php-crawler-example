<?php

use Src\Enum;
use Src\Request;

require '../vendor/autoload.php';

$client = new Request();

$data = $client->getDataFromUrl(Enum::URL);

$result = $client->post($data['cookie'], $data['token'], Enum::URL);

echo 'Gettting answer from url:' . Enum::URL . '...' . PHP_EOL;

echo 'The answer is: ' . $client->getAnswer($result) . PHP_EOL;
