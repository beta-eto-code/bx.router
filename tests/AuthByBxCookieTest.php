<?php

namespace BX\Router\Tests;

use Bitrix\Main\Engine\CurrentUser;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use Bx\Model\Models\User;
use BX\Router\Middlewares\AuthByBxCookie;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthByBxCookieTest extends TestCase
{
    public function testProcess()
    {
        $mockedUserService = $this->createMock(UserServiceInterface::class);
        $mockedUserService
            ->method('getById')
            ->willReturnCallback(function (int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel {
                if ($id > 0) {
                    return new User(['ID' => $id]);
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

        $authBasic = new AuthByBxCookie($mockedUserService);

        CurrentUser::initInstanceWithData(['id' => 1]);
        $request = new ServerRequest('GET', '/test', [], null, '1.1');
        $resultResponse = $authBasic->process($request, $mockedHandler);
        $bodyData = $this->getDataFromResponse($resultResponse);
        $this->assertTrue($bodyData['hasUserContext'] ?? false);

        CurrentUser::initInstanceWithData([]);
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
