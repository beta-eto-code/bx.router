<?php

namespace Bitrix\Main\DI;

class ServiceLocator
{
    private static ?ServiceLocator $instance = null;
    private array $serviceList = [];

    public static function getInstance(): ServiceLocator
    {
        if (static::$instance instanceof ServiceLocator) {
            return static::$instance;
        }

        return static::$instance = new ServiceLocator();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->serviceList[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->serviceList);
    }

    /**
     * @param string $name
     * @param mixed $serviceInstance
     * @return void
     */
    public function addInstance(string $name, $serviceInstance)
    {
        $this->serviceList[$name] = $serviceInstance;
    }
}
