<?php

namespace Bitrix\Main\Routing;

class Route
{
    private string $uri;
    /**
     * @var mixed
     */
    private $controller;

    /**
     * @param string $uri
     * @param mixed $controller
     */
    public function __construct(string $uri, $controller)
    {
        $this->uri = $uri;
        $this->controller = $controller;
    }

    /**
     * @return mixed
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
