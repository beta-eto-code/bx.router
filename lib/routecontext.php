<?php


namespace BX\Router;


use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\RouteContextInterface;
use BX\Router\Middlewares\Cache;
use Psr\Http\Server\MiddlewareInterface;

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

    public function __construct(ExtendRouter $router, ControllerInterface $controller)
    {
        $this->router = $router;
        $this->controller = $controller;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return RouteContextInterface
     */
    public function registerMiddleware(MiddlewareInterface $middleware): RouteContextInterface
    {
        $this->router->registerMiddleware($this->controller, $middleware);
        return $this;
    }

    /**
     * Кешируем ответ сервера
     * @param int $ttl
     * @param string|null $key
     * @return RouteContextInterface
     */
    public function useCache(int $ttl, string $key = null): RouteContextInterface
    {
        $this->router->registerMiddleware($this->controller, new Cache($ttl, $key));
        return $this;
    }
}
