<?php

namespace BX\Router\Tests;

use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use BX\Router\Middlewares\AuthBasic;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class AuthBasicTest extends TestCase
{
    public function testProcess()
    {
        $login = 'testLogin';
        $password = 'testPassword';

        $mockedUserContext = $this->createMock(UserContextInterface::class);
        $mockedUserService = $this->createMock(UserServiceInterface::class);
        $mockedUserService
            ->method('login')
            ->with($login, $password)
            ->willReturn($mockedUserContext);

        $response = new Response(200, [], 'test');
        $mockedHandler = $this->createMock(RequestHandlerInterface::class);
        $mockedHandler->expects($this->once())->method('handle')->willReturn($response);

        $request = new ServerRequest('GET', '/test', [], null, '1.1', [
            'PHP_AUTH_USER' => $login,
            'PHP_AUTH_PW' => $password,
        ]);

        $authBasic = new AuthBasic($mockedUserService);
        $resultResponse = $authBasic->process($request, $mockedHandler);

        $userAttribute = $request->getAttribute('user');
        $this->assertNotNull($userAttribute);
        $this->assertEquals($userAttribute, $mockedUserContext);
        $this->assertEquals($resultResponse, $request);
    }
}
