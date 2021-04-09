<?php


namespace BX\Router;


use Bitrix\Main\Application;
use BX\Router\Bitrix\ExtendedRoutingConfigurator;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\RouteContextInterface;
use BX\Router\Interfaces\RouterInterface;
use BX\Router\Bitrix\ExtendRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @var Application
     */
    private $app;
    /**
     * @var ExtendRouter
     */
    private $bitrixRouter;
    /**
     * @var ExtendedRoutingConfigurator
     */
    private $configurator;

    public function __construct(Application $app, ExtendRouter $bitrixRouter)
    {
        $this->app = $app;
        $this->bitrixRouter = $bitrixRouter;
        $this->configurator = new ExtendedRoutingConfigurator;
        $this->configurator->setRouter($this->bitrixRouter);
    }

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function get(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $this->head($uri, $controller);
        $this->configurator->get($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    private function head(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $proxyController = new ProxyController(
            $controller,
            function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response->withBody(stream_for(''));
            });

        $this->configurator->head($uri, $proxyController);
        return new RouteContext($this->bitrixRouter, $proxyController);
    }

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function post(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $this->configurator->post($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function put(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $this->configurator->put($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    /**
     * @param string $uri
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function delete(string $uri, ControllerInterface $controller): RouteContextInterface
    {
        $this->configurator->delete($uri, $controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }

    /**
     * @param ControllerInterface $controller
     * @return RouteContextInterface
     */
    public function default(ControllerInterface $controller): RouteContextInterface
    {
        $this->bitrixRouter->setDefaultController($controller);
        return new RouteContext($this->bitrixRouter, $controller);
    }
}
