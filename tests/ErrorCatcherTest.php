<?php

namespace BX\Router\Tests;

use BX\Router\AppFactory;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Middlewares\ErrorCatcher;
use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorCatcherTest extends TestCase
{
    public function testProcess(): void
    {
        $mockedBitrixService = $this->createMock(BitrixServiceInterface::class);
        $mockedContainer = $this->createMock(ContainerGetterInterface::class);
        $appFactory = new AppFactory($mockedBitrixService, $mockedContainer);
        $errorCatcherMiddleware = new ErrorCatcher($appFactory);

        $request = new ServerRequest('GET', '/test');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException(new Exception('Неведомая ошибка'));

        $request = $errorCatcherMiddleware->process($request, $handler);
        $this->assertEquals(500, $request->getStatusCode());
        $data = json_decode((string)$request->getBody(), true);
        $this->assertEquals([
            'error' => true,
            'errorMessage' => 'Неведомая ошибка'
        ], $data);
    }
}
