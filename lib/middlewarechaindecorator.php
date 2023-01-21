<?php

namespace BX\Router;

use BX\Router\Interfaces\MiddlewareChainInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareChainDecorator implements MiddlewareChainInterface
{
    private MiddlewareInterface $middleware;
    private ?MiddlewareInterface $nextMiddleware = null;

    public function __construct(MiddlewareInterface $middleware)
    {
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->nextMiddleware instanceof MiddlewareInterface) {
            $handler = $this->createWrappedHandler($handler, $this->nextMiddleware);
        }

        return $this->middleware->process($request, $handler);
    }

    private function createWrappedHandler(
        RequestHandlerInterface $handler,
        MiddlewareInterface $middleware
    ): RequestHandlerInterface {
        return new class($handler, $middleware)
            implements RequestHandlerInterface {
            private RequestHandlerInterface $handler;
            private MiddlewareInterface $middleware;

            public function __construct(RequestHandlerInterface $handler, MiddlewareInterface $middleware)
            {
                $this->handler = $handler;
                $this->middleware = $middleware;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->handler);
            }
        };
    }

    public function addMiddleware(MiddlewareInterface $middleware): MiddlewareChainInterface
    {
        $this->nextMiddleware = new MiddlewareChainDecorator($middleware);
        return $this->nextMiddleware;
    }

    public function getMiddleware(): ?MiddlewareInterface
    {
        return $this->nextMiddleware;
    }
}
