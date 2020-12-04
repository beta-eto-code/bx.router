<?php


namespace BX\Router\Interfaces;

use BX\Router\ResponseHandler;

interface RestAppInterface
{
    /**
     * @return AppFactoryInterface
     */
    public function getFactory(): AppFactoryInterface;

    /**
     * Добавляем сервис в DI контейнер
     * @param string $name
     * @param $serviceInstance
     * @return mixed
     */
    public function setService(string $name, $serviceInstance);

    /**
     * @param ResponseHandler $responseHandler
     * @return mixed
     */
    public function setResponseHandler(ResponseHandler $responseHandler);

    /**
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface;

    /**
     * @return mixed
     */
    public function run();
}
