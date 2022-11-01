<?php

namespace BX\Router\Middlewares;

use Bx\Model\Interfaces\UserContextInterface;
use BX\Router\Exceptions\ForbiddenException;
use BX\Router\Exceptions\UnauthorizedException;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckAccessWithScope implements MiddlewareChainInterface
{
    use ChainHelper;

    private string $scope = '';
    private array $roleList = [];

    public function __construct(string $scope, int ...$roleList)
    {
        $this->scope = $scope;
        $this->roleList = $roleList;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var UserContextInterface|null $userContext
         */
        $userContext = $request->getAttribute('user') ?? null;
        if (empty($userContext)) {
            throw new UnauthorizedException('Пользователь не авторизован');
        }

        foreach ($this->roleList as $role) {
            if ($userContext->hasAccessOperation($role, $this->scope)) {
                return $this->runChain($request, $handler);
            }
        }

        throw new ForbiddenException('Нет доступа');
    }
}
