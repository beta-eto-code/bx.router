<?php


namespace BX\Router\Interfaces;


use Psr\Http\Server\RequestHandlerInterface;

interface ControllerInterface extends RequestHandlerInterface
{
    /**
     * @param BitrixServiceInterface $bitrixService
     * @return mixed
     */
    public function setBitrixService(BitrixServiceInterface $bitrixService);

    /**
     * @param AppFactoryInterface $appFactory
     * @return mixed
     */
    public function setAppFactory(AppFactoryInterface $appFactory);

    /**
     * @param ContainerGetterInterface $containerGetter
     * @return mixed
     */
    public function setContainer(ContainerGetterInterface $containerGetter);
}
