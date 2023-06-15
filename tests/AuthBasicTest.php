<?php

namespace BX\Router\Tests;

use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use BX\Router\Middlewares\AuthBasic;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
            ->willReturnCallback(function (
                string $currentLogin,
                string $currentPassword
            ) use (
                $login,
                $password,
                $mockedUserContext
            ): ?UserContextInterface {
                if ($login === $currentLogin && $password === $currentPassword) {
                    return $mockedUserContext;
                }

                return null;
            });

        $mockedHandler = $this->createMock(RequestHandlerInterface::class);
        $mockedHandler->expects($this->any())->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request): ResponseInterface {
                $userAttribute = $request->getAttribute('user');
                $response = new Response(200, [], 'test');
                $response->getBody()->write(json_encode([
                    'hasUserContext' => !empty($userAttribute)
                ]));
                return $response;
            });

        $authBasic = new AuthBasic($mockedUserService);

        $request = new ServerRequest('GET', '/test', [], null, '1.1', [
            'PHP_AUTH_USER' => $login,
            'PHP_AUTH_PW' => $password,
        ]);
        $resultResponse = $authBasic->process($request, $mockedHandler);
        $bodyData = $this->getDataFromResponse($resultResponse);
        $this->assertTrue($bodyData['hasUserContext'] ?? false);

        $request = new ServerRequest('GET', '/test', [], null, '1.1', [
            'PHP_AUTH_USER' => $login,
            'PHP_AUTH_PW' => "invalid password",
        ]);
        $resultResponse = $authBasic->process($request, $mockedHandler);
        $bodyData = $this->getDataFromResponse($resultResponse);
        $this->assertFalse($bodyData['hasUserContext'] ?? false);
    }

    private function getDataFromResponse(ResponseInterface $response): array
    {
        $rawBody = (string) $response->getBody();
        return json_decode($rawBody, true);
    }
}
