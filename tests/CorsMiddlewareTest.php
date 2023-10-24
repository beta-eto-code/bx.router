<?php

namespace BX\Router\Tests;

use BX\Router\AppFactory;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Middlewares\CorsMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddlewareTest extends TestCase
{
    public function testProcess(): void
    {
        $mockedBitrixService = $this->createMock(BitrixServiceInterface::class);
        $mockedContainer = $this->createMock(ContainerGetterInterface::class);
        $appFactory = new AppFactory($mockedBitrixService, $mockedContainer);
        $corsMiddleware = new CorsMiddleware(
            $appFactory,
            [
                'http://test.ru',
                'https://one-more-test.ru'
            ],
            [
                'GET'
            ]
        );

        $request = new ServerRequest('GET', 'http://test.ru/1234', ['Origin' => 'http://test.ru']);
        $mockedHandler = $this->createMock(RequestHandlerInterface::class);
        $mockedHandler->method('handle')->willReturn(new Response());
        $response = $corsMiddleware->process($request, $mockedHandler);
        $this->assertEquals(['http://test.ru'], $response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(['*'], $response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEquals(['GET'], $response->getHeader('Access-Control-Allow-Methods'));

        $request = new ServerRequest(
            'GET',
            'http://test.ru/1234',
            [
                'Referer' => 'https://one-more-test.ru',
                'Origin' => 'http://test.ru'
            ]
        );
        $response = $corsMiddleware->process($request, $mockedHandler);
        $this->assertEquals(['http://test.ru'], $response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(['*'], $response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEquals(['GET'], $response->getHeader('Access-Control-Allow-Methods'));

        $request = new ServerRequest(
            'GET',
            'http://test.ru/1234',
            [
                'Referer' => 'https://one-more-test.ru',
            ]
        );
        $response = $corsMiddleware->process($request, $mockedHandler);
        $this->assertEquals(['https://one-more-test.ru'], $response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals(['*'], $response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEquals(['GET'], $response->getHeader('Access-Control-Allow-Methods'));

        $request = new ServerRequest(
            'POST',
            'http://test.ru/1234',
            [
                'Referer' => 'https://one-more-test.ru',
                'Origin' => 'http://test.ru'
            ]
        );
        $response = $corsMiddleware->process($request, $mockedHandler);
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Headers'));
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Methods'));

        $request = new ServerRequest(
            'OPTIONS',
            'http://test.ru/1234',
            [
                'Origin' => 'http://test.ru'
            ]
        );
        $response = $corsMiddleware->process($request, $mockedHandler);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
