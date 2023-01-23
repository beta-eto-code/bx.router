<?php

namespace BX\Router;

use Bitrix\Main\Application;
use BX\Router\Bitrix\ExtendedRoutingConfigurator;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\RouteContextInterface;
use BX\Router\Interfaces\RouterInterface;
use BX\Router\Bitrix\ExtendRouter;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    public Application $app;
    /**
     * @psalm-suppress MissingDependency
     */
    private ExtendRouter $bitrixRouter;
    /**
     * @psalm-suppress MissingDependency
     */
    private ExtendedRoutingConfigurator $configurator;

    /**
     * @psalm-suppress MissingDependency
     */
    public function __construct(Application $app, ExtendRouter $bitrixRouter)
    {
        $this->app = $app;
        $this->bitrixRouter = $bitrixRouter;
        $this->configurator = new ExtendedRoutingConfigurator();
        /**
         * @psalm-suppress UndefinedMagicMethod
         */
        $this->configurator->setRouter($this->bitrixRouter);
    }

    public function get(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $this->head($uri, $controller);
        /**
         * @psalm-suppress MissingDependency
         */
        $this->configurator->get($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    private function head(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $proxyController = new ProxyController(
            $controller,
            function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response->withBody(Utils::streamFor(''));
            }
        );

        /**
         * @psalm-suppress MissingDependency
         */
        $this->configurator->head($uri, $proxyController);
        return new RouteContext($this->bitrixRouter, $proxyController);
    }

    public function post(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        /**
         * @psalm-suppress MissingDependency
         */
        $this->configurator->post($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }


    public function put(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        /**
         * @psalm-suppress MissingDependency
         */
        $this->configurator->put($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    public function delete(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        /**
         * @psalm-suppress MissingDependency
         */
        $this->configurator->delete($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    public function default(ControllerInterface $controller): RouteContextInterface
    {
        /**
         * @psalm-suppress MissingDependency
         */
        $this->bitrixRouter->setDefaultController($controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }
}
