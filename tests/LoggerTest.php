<?php

namespace BX\Router\Tests;

use Bitrix\Main\Entity\DataManager;
use BX\Router\Middlewares\Logger;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testProcess(): void
    {
        $mockedLogTable = $this->createMock(DataManager::class);

        $method = 'POST';
        $url = '/test';
        $requestBody = '{"key1": 1, "key2": 2}';
        $requestHeaders = ['Content-type' => 'application/json'];
        $request = new ServerRequest($method, $url, $requestHeaders, $requestBody);
/*        $logData = [
            'url' => $request->getRequestTarget(),
            'method' => $request->getMethod(),
            'controller' => get_class($handler),
            'request_body' => (string)$request->getBody(),
            'response_body' => (string)$response->getBody(),
            'request_headers' => json_encode($request->getHeaders()),
            'response_headers' => json_encode($response->getHeaders()),
            'date_create' => new DateTime(),
            'status' => $response->getStatusCode(),
        ]
        $mockedLogTable->method("add")->with([])*/
        $loggerMiddleware = new Logger($mockedLogTable);
    }
}
