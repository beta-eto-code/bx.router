<?php

namespace BX\Router\Middlewares\Traits;

use BX\Router\Interfaces\MiddlewareChainInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplObjectStorage;

trait ChainHelper
{
    protected ?SplObjectStorage $store = null;

    public function addMiddleware(MiddlewareInterface $middleware): MiddlewareChainInterface
    {
        if (empty($this->store)) {
            $this->store = new SplObjectStorage();  // для избежания циклических зависимостей
        }

        /**
         * @psalm-suppress InvalidArgument
         */
        if ($this->store->contains($middleware)) {
            return $this;
        }

        $this->store->rewind();
        $internal = $this->store->current();
        if ($internal instanceof MiddlewareInterface) {
            /**
             * @psalm-suppress UndefinedInterfaceMethod
             */
            return $internal->addMiddleware($middleware);
        }

        /**
         * @psalm-suppress InvalidArgument
         */
        $this->store->attach($middleware);

        return $this;
    }

    /**
     * @return MiddlewareInterface|null
     * @psalm-suppress LessSpecificImplementedReturnType,MoreSpecificReturnType
     */
    public function getMiddleware(): ?MiddlewareInterface
    {
        if (empty($this->store)) {
            return null;
        }

        $this->store->rewind();
        return $this->store->current();
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
