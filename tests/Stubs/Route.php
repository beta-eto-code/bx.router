<?php

namespace Bitrix\Main\Routing;

class Route
{
    private string $uri;
    /**
     * @var callable
     */
    private $controller;

    public function __construct(string $uri, callable $controller)
    {
        $this->uri = $uri;
        $this->controller = $controller;
    }

    /**
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getParametersValues()
    {
    }
}
