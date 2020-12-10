<?php


namespace BX\Router\Middlewares\Traits;

use BX\Router\Interfaces\MiddlewareChainInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait ChainHelper
{
    /**
     * @var MiddlewareChainInterface
     */
    protected $middleware;

    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function addMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface
    {
        if ($this->middleware instanceof MiddlewareChainInterface) {
            return $this->middleware->addMiddleware($middleware);
        }

        return $this->middleware = $middleware;
    }

    /**
     * @return MiddlewareChainInterface|null
     */
    public function getMiddleware(): ?MiddlewareChainInterface
    {
        return $this->middleware;
    }

    protected function runChain(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if ($middleware instanceof MiddlewareInterface) {
            $response = $middleware->process($request, $handler);
        } else {
            $response = $handler->handle($request);
        }

        return $response;
    }
}
