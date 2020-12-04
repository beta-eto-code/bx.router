<?php


namespace BX\Router\Interfaces;


use Psr\Http\Server\MiddlewareInterface;

interface RouteContextInterface
{
    /**
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function registerMiddleware(MiddlewareInterface $middleware): self;

    /**
     * @param int $ttl
     * @param string|null $key
     * @return $this
     */
    public function useCache(int $ttl, string $key = null): self;
}
