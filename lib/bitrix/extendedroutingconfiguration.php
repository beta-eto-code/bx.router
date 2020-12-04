<?php

namespace BX\Router\Bitrix;

use Bitrix\Main\Routing\Route;
use Bitrix\Main\Routing\RoutingConfiguration;

class ExtendedRoutingConfiguration extends RoutingConfiguration
{
    public static $configurationList = [
        'get', 'post', 'put', 'delete', 'any', 'group'
    ];

    public function put($uri, $controller)
    {
        $this->options->methods(['PUT']);

        $route = new Route($uri, $controller);
        $this->routeContainer = $route;

        return $this;
    }

    public function delete($uri, $controller)
    {
        $this->options->methods(['DELETE']);

        $route = new Route($uri, $controller);
        $this->routeContainer = $route;

        return $this;
    }
}
