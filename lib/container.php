<?php

namespace BX\Router;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\ObjectNotFoundException;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Interfaces\ContainerSetterInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

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

    /**
     * @param string $name
     * @return mixed
     * @throws ObjectNotFoundException|NotFoundExceptionInterface
     */
    public function get(string $name)
    {
        $result = $this->container->get($name);
        if (is_callable($result)) {
            try {
                $result = $result();
                $this->set($name, $result);
            } catch (Throwable $e) {
                return null;
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->container->has($name);
    }

    /**
     * @param string $name
     * @param $serviceInstance
     * @return void
     */
    public function set(string $name, $serviceInstance)
    {
        $this->container->addInstance($name, $serviceInstance);
    }
}
