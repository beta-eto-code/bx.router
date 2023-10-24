<?php

namespace BX\Router\Middlewares;

use Bx\Model\Interfaces\AccessStrategyInterface;
use Bx\Model\Interfaces\UserContextInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use BX\Router\Interfaces\AppFactoryInterface;
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
     * @var ?AccessStrategyInterface
     */
    private $accessStrategy;
    /**
     * @var bool
     */
    private $authIsRequired;

    /**
     * @var AppFactoryInterface|null
     */
    private $appFactory = null;

    /**
     * @var string|null
     */
    private $notificationMessage = null;

    /**
     * AuthBasic constructor.
     * @param UserServiceInterface $userService
     * @param AccessStrategyInterface|null $accessStrategy
     * @param bool $authIsRequired
     */
    public function __construct(UserServiceInterface $userService, ?AccessStrategyInterface $accessStrategy = null) {
        $this->authIsRequired = false;
        $this->userService = $userService;
        $this->accessStrategy = $accessStrategy;
    }

    public function setRequiredAuthMode(AppFactoryInterface $appFactory, ?string $notificationMessage = null): void
    {
        $this->authIsRequired = true;
        $this->appFactory = $appFactory;
        $this->notificationMessage = $notificationMessage;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getServerParams();
        $user = (string)$params['PHP_AUTH_USER'];
        $password = (string)$params['PHP_AUTH_PW'];

        $userContext = $this->userService->login($user, $password);
        if ($this->authIsRequired && empty($userContext) && $this->appFactory instanceof AppFactoryInterface) {
            $response = $this->appFactory->createJsonResponse([
                'message' => $this->notificationMessage ?: 'Необходимо авторизоваться'
            ], 401);
            return $response->withHeader('WWW-Authenticate', 'Basic');
        }

        if ($userContext instanceof UserContextInterface && $this->accessStrategy instanceof AccessStrategyInterface) {
            $userContext->setAccessStrategy($this->accessStrategy);
        }

        if ($userContext instanceof UserContextInterface) {
            return $this->runChain($request->withAttribute('user', $userContext), $handler);
        }

        return $this->runChain($request, $handler);
    }
}
