<?php

namespace Bitrix\Main\Routing;

class RoutingConfigurator
{
    protected Router $router;
    protected Options $scopeOptions;

    public function __construct()
    {
        $this->router = new Router();
        $this->scopeOptions = new Options();
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
    }

    /**
     * @param Router $router
     * @return void
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }
}
