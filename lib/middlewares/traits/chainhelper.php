<?php


namespace BX\Router\Middlewares\Traits;

use BX\Router\Interfaces\MiddlewareChainInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplObjectStorage;
use Throwable;

trait ChainHelper
{
    /**
     * @var MiddlewareChainInterface
     */
    protected $middleware;
    protected $store;

    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function addMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface
    {
        if (empty($this->store)) {
            $this->store = new SplObjectStorage();  // для избежания циклических зависимостей
        }

        if ($this->store->contains($middleware)) {
            return $this;
        }

        $this->store->rewind();

        try {
            $internal = $this->store->current();
        } catch (Throwable $e) {
            $internal = false;
        }

        if ($internal instanceof MiddlewareChainInterface) {
            return $internal->addMiddleware($middleware);
        }

        $this->store->attach($middleware);

        return $this;
    }

    /**
     * @return MiddlewareChainInterface|null
     */
    public function getMiddleware(): ?MiddlewareChainInterface
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
