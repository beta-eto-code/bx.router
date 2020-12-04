<?php


namespace BX\Router;


use Bitrix\Main\DI\ServiceLocator;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Interfaces\ContainerSetterInterface;

class Container implements ContainerSetterInterface, ContainerGetterInterface
{
    /**
     * @var ServiceLocator
     */
    private $container;

    public function __construct()
    {
        $this->container = ServiceLocator::getInstance();
    }

    public function get(string $name)
    {
        return $this->container->get($name);
    }

    public function has(string $name): bool
    {
        return $this->container->has($name);
    }

    public function set(string $name, $serviceInstance)
    {
        $this->container->addInstance($name, $serviceInstance);
    }
}
