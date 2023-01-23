<?php

namespace BX\Router;

use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Interfaces\RouteContextInterface;
use BX\Router\Middlewares\Cache;
use Psr\Http\Server\MiddlewareInterface;

class RouteContext implements RouteContextInterface
{
    /**
     * @psalm-suppress MissingDependency
     */
    private ExtendRouter $router;
    private ControllerInterface $controller;
    private ?MiddlewareChainInterface $middleware = null;

    /**
     * @psalm-suppress MissingDependency
     */
    public function __construct(ExtendRouter $router, ControllerInterface $controller)
    {
        $this->router = $router;
        $this->controller = $controller;
    }

    public function registerMiddleware(MiddlewareInterface $middleware): MiddlewareChainInterface
    {
        $this->middleware = new MiddlewareChainDecorator($middleware);
        /**
         * @psalm-suppress MissingDependency
         */
        return $this->router->registerMiddleware($this->controller, $this->middleware);
    }

    /**
     * Кешируем ответ сервера
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function useCache(int $ttl, string $key = null): RouteContextInterface
    {
        if ($this->middleware instanceof MiddlewareChainInterface) {
            $this->middleware->addMiddleware(new Cache($ttl, $key));
        } else {
            $this->middleware = new Cache($ttl, $key);
        }

        /**
         * @psalm-suppress MissingDependency
         */
        $this->router->registerMiddleware($this->controller, $this->middleware);
        return $this;
    }

    public function useCacheWithKeyCallback(int $ttl, callable $fnKeyCalculate): RouteContextInterface
    {
        $cacheMiddleware = new Cache($ttl);
        $cacheMiddleware->setKeyCalculateCallback($fnKeyCalculate);
        if ($this->middleware instanceof MiddlewareChainInterface) {
            $this->middleware->addMiddleware($cacheMiddleware);
        } else {
            $this->middleware = new MiddlewareChainDecorator($cacheMiddleware);
        }

        /**
         * @psalm-suppress MissingDependency
         */
        $this->router->registerMiddleware($this->controller, $this->middleware);
        return $this;
    }
}
