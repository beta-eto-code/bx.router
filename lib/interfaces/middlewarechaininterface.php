<?php


namespace BX\Router\Interfaces;


use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareChainInterface extends MiddlewareInterface
{
    /**
     * @param MiddlewareInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function addMiddleware(MiddlewareInterface $middleware): MiddlewareChainInterface;

    /**
     * @return MiddlewareChainInterface|null
     */
    public function getMiddleware(): ?MiddlewareInterface;
}
