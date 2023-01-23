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

class CheckAccess implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @var int[]
     */
    private array $roleList;

    public function __construct(int ...$roleList)
    {
        $this->roleList = $roleList;
    }

    /**
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
            if ($userContext->hasAccessOperation($role)) {
                return $this->runChain($request, $handler);
            }
        }

        throw new ForbiddenException('Нет доступа');
    }
}
