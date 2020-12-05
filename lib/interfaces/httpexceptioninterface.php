<?php


namespace BX\Router\Interfaces;


use Psr\Http\Message\ResponseInterface;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;

interface HttpExceptionInterface extends Throwable
{
    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function setRequest(ServerRequestInterface $request);

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface;

    /**
     * @param AppFactoryInterface|null $appFactory
     * @return ResponseInterface
     */
    public function getResponse(AppFactoryInterface $appFactory = null): ?ResponseInterface;
}
