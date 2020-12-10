<?php


namespace BX\Router;


use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Interfaces\RouteContextInterface;
use BX\Router\Middlewares\Cache;

class RouteContext implements RouteContextInterface
{
    /**
     * @var ExtendRouter
     */
    private $router;
    /**
     * @var ControllerInterface
     */
    private $controller;
    /**
     * @var MiddlewareChainInterface
     */
    private $middleware;

    public function __construct(ExtendRouter $router, ControllerInterface $controller)
    {
        $this->router = $router;
        $this->controller = $controller;
    }

    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface
    {
        $this->middleware = $middleware;
        return $this->router->registerMiddleware($this->controller, $this->middleware);
    }

    /**
     * Кешируем ответ сервера
     * @param int $ttl
     * @param string|null $key
     * @return RouteContextInterface
     */
    public function useCache(int $ttl, string $key = null): RouteContextInterface
    {
        if ($this->middleware instanceof MiddlewareChainInterface) {
            $this->middleware->addMiddleware(new Cache($ttl, $key));
        } else {
            $this->middleware = new Cache($ttl, $key);
        }

        $this->router->registerMiddleware($this->controller, $this->middleware);
        return $this;
    }
}
