<?php

namespace Bitrix\Main\Routing;

class Router
{
    protected array $configurations = [];

    /**
     * @param mixed $configuration
     * @return void
     */
    public function registerConfiguration($configuration)
    {
        $this->configurations[] = $configuration;
    }

    /**
     * @param mixed $request
     * @return mixed void
     */
    public function match($request)
    {
    }

    /**
     * @return void
     */
    public function releaseRoutes()
    {
    }
}
