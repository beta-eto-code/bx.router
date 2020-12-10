<?php


namespace BX\Router\Interfaces;

interface RouteContextInterface
{
    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface;

    /**
     * @param int $ttl
     * @param string|null $key
     * @return $this
     */
    public function useCache(int $ttl, string $key = null): self;
}
