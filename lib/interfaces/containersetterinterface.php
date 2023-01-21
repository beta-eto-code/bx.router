<?php

namespace BX\Router\Interfaces;

interface ContainerSetterInterface
{
    /**
     * @param string $name
     * @param mixed $serviceInstance
     * @return void
     */
    public function set(string $name, $serviceInstance);
}
