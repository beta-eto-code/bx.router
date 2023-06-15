<?php

namespace BX\Router\Bitrix;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Routing\Route;
use Bitrix\Main\Routing\Router;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use SplObjectStorage;

/**
 * @psalm-suppress MissingDependency
 */
class ExtendRouter extends Router
{
    private SplObjectStorage $storage;
    private ?ControllerInterface $defaultController = null;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    /**
     * @param ControllerInterface $controller
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(
        ControllerInterface $controller,
        MiddlewareChainInterface $middleware
    ): MiddlewareChainInterface {
        return $this->storage[$controller] = $middleware;
    }

    /**
     * @param ControllerInterface $controller
     * @return MiddlewareChainInterface|null
     */
    public function getMiddlewaresByController(ControllerInterface $controller): ?MiddlewareChainInterface
    {
        return $this->storage[$controller] ?? null;
    }

    /**
     * @param ControllerInterface $controller
     */
    public function setDefaultController(ControllerInterface $controller): void
    {
        $this->defaultController = $controller;
    }

    /**
     * @param HttpRequest $request
     * @return Route|void
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function match($request)
    {
        /**
         * @psalm-suppress UndefinedMethod
         */
        return parent::match($request) ?? new Route($request->getRequestUri(), $this->defaultController);
    }
}
