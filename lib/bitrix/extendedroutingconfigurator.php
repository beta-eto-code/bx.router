<?php


namespace BX\Router\Bitrix;


use Bitrix\Main\Routing\RoutingConfiguration;
use Bitrix\Main\Routing\RoutingConfigurator;

/**
 * @package    bitrix
 * @subpackage main
 *
 * @method RoutingConfiguration middleware($middleware)
 * @method RoutingConfiguration prefix($prefix)
 * @method RoutingConfiguration name($name)
 * @method RoutingConfiguration domain($domain)
 * @method RoutingConfiguration where($parameter, $pattern)
 * @method RoutingConfiguration default($parameter, $value)
 *
 * @method RoutingConfiguration get($uri, $controller)
 * @method RoutingConfiguration head($uri, $controller)
 * @method RoutingConfiguration post($uri, $controller)
 * @method RoutingConfiguration put($uri, $controller)
 * @method RoutingConfiguration delete($uri, $controller)
 * @method RoutingConfiguration any($uri, $controller)
 *
 * @method RoutingConfiguration group($callback)
 */
class ExtendedRoutingConfigurator extends RoutingConfigurator
{
    public function __call($method, $arguments)
    {
        // setting extend route
        if (in_array($method, ExtendedRoutingConfiguration::$configurationList, true))
        {
            $configuration = $this->createExtendConfiguration();
            return $configuration->$method(...$arguments);
        }

        return parent::__call($method, $arguments);
    }

    /**
     * @return ExtendedRoutingConfiguration
     */
    public function createExtendConfiguration()
    {
        $configuration = new ExtendedRoutingConfiguration;

        $configuration->setConfigurator($this);
        $this->router->registerConfiguration($configuration);

        $configuration->setOptions(clone $this->scopeOptions);

        return $configuration;
    }
}
