<?php


namespace BX\Router;


use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Interfaces\ControllerInterface;

abstract class BaseController implements ControllerInterface
{

    /**
     * @var BitrixServiceInterface
     */
    protected $bitrixService;
    /**
     * @var AppFactoryInterface
     */
    protected $appFactory;
    /**
     * @var ContainerGetterInterface
     */
    protected $container;

    public function setBitrixService(BitrixServiceInterface $bitrixService)
    {
        $this->bitrixService = $bitrixService;
    }

    public function setAppFactory(AppFactoryInterface $appFactory)
    {
        $this->appFactory = $appFactory;
    }

    public function setContainer(ContainerGetterInterface $containerGetter)
    {
        $this->container = $containerGetter;
    }
}
