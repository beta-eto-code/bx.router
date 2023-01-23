<?php

namespace Bitrix\Main;

class Application
{
    private static ?Application $instance = null;
    private Context $context;

    public function __construct()
    {
        $this->context = new Context();
    }

    public static function getInstance(): Application
    {
        if (static::$instance instanceof Application) {
            return static::$instance;
        }
        return static::$instance = new Application();
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return void
     */
    public function terminate()
    {
    }
}
