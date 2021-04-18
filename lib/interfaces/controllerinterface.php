<?php


namespace BX\Router\Interfaces;


use Psr\Http\Server\RequestHandlerInterface;

interface ControllerInterface extends RequestHandlerInterface
{
    /**
     * @param BitrixServiceInterface $bitrixService
     * @return void
     */
    public function setBitrixService(BitrixServiceInterface $bitrixService);

    /**
     * @param AppFactoryInterface $appFactory
     * @return void
     */
    public function setAppFactory(AppFactoryInterface $appFactory);

    /**
     * @param ContainerGetterInterface $containerGetter
     * @return void
     */
    public function setContainer(ContainerGetterInterface $containerGetter);
}
