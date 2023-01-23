<?php

namespace BX\Router\Bitrix;

use Bitrix\Main\Routing\Route;
use Bitrix\Main\Routing\RoutingConfiguration;

class ExtendedRoutingConfiguration extends RoutingConfiguration
{
    /**
     * @var string[]
     */
    public static $configurationList = [
        'get', 'post', 'put', 'delete', 'head', 'any', 'group'
    ];

    /**
     * @param string $uri
     * @param callable $controller
     * @return ExtendedRoutingConfiguration
     */
    public function head($uri, $controller): ExtendedRoutingConfiguration
    {
        $this->options->methods(['HEAD']);

        $route = new Route($uri, $controller);
        $this->routeContainer = $route;

        return $this;
    }

    /**
     * @param string $uri
     * @param callable $controller
     * @return ExtendedRoutingConfiguration
     */
    public function put($uri, $controller): ExtendedRoutingConfiguration
    {
        $this->options->methods(['PUT']);

        $route = new Route($uri, $controller);
        $this->routeContainer = $route;

        return $this;
    }

    /**
     * @param string $uri
     * @param callable $controller
     * @return ExtendedRoutingConfiguration
     */
    public function delete($uri, $controller): ExtendedRoutingConfiguration
    {
        $this->options->methods(['DELETE']);

        $route = new Route($uri, $controller);
        $this->routeContainer = $route;

        return $this;
    }
}
