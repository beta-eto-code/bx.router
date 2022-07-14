<?php

namespace BX\Router\Middlewares;

use Bitrix\Main\Engine\CurrentUser;
use Bx\Model\Interfaces\AccessStrategyInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use Bx\Model\Models\User;
use Bx\Model\UserContext;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthByBxCookie implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @var UserServiceInterface
     */
    private UserServiceInterface $userService;
    /**
     * @var AccessStrategyInterface|null
     */
    private ?AccessStrategyInterface $accessStrategy;

    /**
     * AuthBasic constructor.
     * @param UserServiceInterface $userService
     * @param AccessStrategyInterface|null $accessStrategy
     */
    public function __construct(UserServiceInterface $userService, AccessStrategyInterface $accessStrategy = null)
    {
        $this->userService = $userService;
        $this->accessStrategy = $accessStrategy;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userId = (int) CurrentUser::get()->getId();
        if ($userId === 0) {
            return $this->runChain($request, $handler);
        }

        /**
         * @var User|null $user
         */
        $user = $this->userService->getById($userId);
        if (empty($user)) {
            return $this->runChain($request, $handler);
        }

        $userContext = new UserContext($user);
        if ($this->accessStrategy instanceof AccessStrategyInterface) {
            $userContext->setAccessStrategy($this->accessStrategy);
        }

        return $this->runChain($request->withAttribute('user', $userContext), $handler);
    }
}
