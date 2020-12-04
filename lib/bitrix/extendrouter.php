<?php


namespace BX\Router\Bitrix;


use Bitrix\Main\HttpRequest;
use Bitrix\Main\Routing\Route;
use Bitrix\Main\Routing\Router;
use BX\Router\Interfaces\ControllerInterface;
use Psr\Http\Server\MiddlewareInterface;
use SplObjectStorage;

class ExtendRouter extends Router
{
    /**
     * @var SplObjectStorage
     */
    private $storage;
    /**
     * @var ControllerInterface|null
     */
    private $defaultController;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    /**
     * @param ControllerInterface $controller
     * @param MiddlewareInterface $middleware
     */
    public function registerMiddleware(ControllerInterface $controller, MiddlewareInterface $middleware)
    {
        $className = get_class($middleware);
        $list = $this->getMiddlewaresByController($controller);
        $list[$className] = $middleware;
        $this->storage[$controller] = $list;
    }

    /**
     * @param ControllerInterface $controller
     * @return MiddlewareInterface[]
     */
    public function getMiddlewaresByController(ControllerInterface $controller): array
    {
        return $this->storage[$controller] ?? [];
    }

    /**
     * @param ControllerInterface $controller
     */
    public function setDefaultController(ControllerInterface $controller)
    {
        $this->defaultController = $controller;
    }

    /**
     * @param HttpRequest $request
     * @return Route|void
     */
    public function match($request)
    {
        return parent::match($request) ?? new Route($request->getRequestUri(), $this->defaultController);
    }
}
