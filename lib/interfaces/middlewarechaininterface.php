<?php


namespace BX\Router\Interfaces;


use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareChainInterface extends MiddlewareInterface
{
    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function addMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface;

    /**
     * @return MiddlewareChainInterface|null
     */
    public function getMiddleware(): ?MiddlewareChainInterface;
}
