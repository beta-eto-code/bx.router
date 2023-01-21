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
     * @var ExtendRouter
     * @psalm-suppress MissingDependency
     */
    private $router;
    /**
     * @var ControllerInterface
     */
    private $controller;
    /**
     * @var ?MiddlewareChainInterface
     */
    private $middleware;

    /**
     * @param ExtendRouter $router
     * @param ControllerInterface $controller
     * @psalm-suppress MissingDependency
     */
    public function __construct(ExtendRouter $router, ControllerInterface $controller)
    {
        $this->router = $router;
        $this->controller = $controller;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return MiddlewareChainInterface
     */
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
     * @param int $ttl
     * @param string|null $key
     * @return RouteContextInterface
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

    /**
     * @param int $ttl
     * @param callable $fnKeyCalculate
     * @return $this
     */
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
