<?php

namespace BX\Router\Middlewares;

use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\HttpExceptionInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpException implements MiddlewareChainInterface
{
    use ChainHelper;

    private AppFactoryInterface $appFactory;

    public function __construct(AppFactoryInterface $appFactory)
    {
        $this->appFactory = $appFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->runChain($request, $handler);
        } catch (HttpExceptionInterface $e) {
            $e->setRequest($request);
            $response = $e->getResponse($this->appFactory);

            return $response instanceof ResponseInterface ? $response : $this->appFactory->createJsonResponse([
                'error' => true,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }
}
