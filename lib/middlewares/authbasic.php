<?php

namespace BX\Router\Middlewares;

use Bx\Model\Interfaces\AccessStrategyInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthBasic implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var AccessStrategyInterface|null
     */
    private $accessStrategy;

    /**
     * @param UserServiceInterface $userService
     * @param AccessStrategyInterface|null $accessStrategy
     */
    public function __construct(UserServiceInterface $userService, ?AccessStrategyInterface $accessStrategy = null)
    {
        $this->userService = $userService;
        $this->accessStrategy = $accessStrategy;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getServerParams();
        $user = (string)$params['PHP_AUTH_USER'];
        $password = (string)$params['PHP_AUTH_PW'];

        $userContext = $this->userService->login($user, $password);
        if ($userContext instanceof UserContextInterface && $this->accessStrategy instanceof AccessStrategyInterface) {
            $userContext->setAccessStrategy($this->accessStrategy);
        }

        if ($userContext instanceof UserContextInterface) {
            return $this->runChain($request->withAttribute('user', $userContext), $handler);
        }

        return $this->runChain($request, $handler);
    }
}
