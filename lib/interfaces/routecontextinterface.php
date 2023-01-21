<?php

namespace BX\Router\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface RouteContextInterface
{
    /**
     * @param MiddlewareInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(MiddlewareInterface $middleware): MiddlewareChainInterface;

    /**
     * @param int $ttl
     * @param string|null $key
     * @return $this
     */
    public function useCache(int $ttl, string $key = null): self;

    /**
     * @param int $ttl
     * @param callable $fnKeyCalculate
     * @return $this
     */
    public function useCacheWithKeyCallback(int $ttl, callable $fnKeyCalculate): self;
}
