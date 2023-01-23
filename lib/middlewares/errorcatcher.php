<?php

namespace BX\Router\Middlewares;

use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ErrorCatcher implements MiddlewareChainInterface
{
    use ChainHelper;

    private AppFactoryInterface $factory;

    public function __construct(AppFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->runChain($request, $handler);
        } catch (Throwable $e) {
            return $this->factory->createJsonResponse([
                'error' => true,
                'errorMessage' => $e->getMessage(),
            ], 500);
        }
    }
}
