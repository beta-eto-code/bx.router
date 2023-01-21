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
     * @return void
     */
    public function setService(string $name, $serviceInstance);

    /**
     * @param ResponseHandler $responseHandler
     * @return void
     */
    public function setResponseHandler(ResponseHandler $responseHandler);

    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface;

    /**
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface;

    /**
     * @return void
     */
    public function run();
}
