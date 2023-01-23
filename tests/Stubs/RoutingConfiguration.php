<?php

namespace Bitrix\Main\Routing;

class RoutingConfiguration
{
    protected Options $options;
    protected ?Route $routeContainer = null;
    /**
     * @var mixed
     */
    private $configurator;

    public function __construct()
    {
        $this->options = new Options();
    }

    /**
     * @param $configurator
     * @return void
     */
    public function setConfigurator($configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @param Options $options
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
