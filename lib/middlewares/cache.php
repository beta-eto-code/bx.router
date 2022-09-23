<?php

namespace BX\Router\Middlewares;

use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bitrix\Main\Data\Cache as BitrixCache;
use Throwable;

class Cache implements MiddlewareChainInterface
{
    use ChainHelper;

    public const CACHE_DIR = 'router_cache';
    public const ALLOW_METHOD = 'get';

    /**
     * @var int
     */
    private $ttl;
    /**
     * @var string|null
     */
    private $key;

    public function __construct(int $ttl, string $key = null)
    {
        $this->ttl = $ttl;
        $this->key = $key;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getCacheKey(ServerRequestInterface $request): string
    {
        if (!empty($this->key)) {
            return $this->key;
        }

        $data = [
            'uri' => $request->getRequestTarget(),
            'query' => $request->getQueryParams(),
            'attributes' => $request->getAttributes(),
        ];

        return 'query_' . md5(serialize($data));
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $currentMethod = trim(strtolower($request->getMethod()));
        if ($currentMethod !== static::ALLOW_METHOD) {
            return $handler->handle($request);
        }

        $response = null;
        $key = $this->getCacheKey($request);
        $cache = BitrixCache::createInstance();
        if ($cache->initCache($this->ttl, $key, static::CACHE_DIR)) {
            $responseData = $cache->getVars();
            $response = unserialize($responseData);
        } elseif ($cache->startDataCache()) {
            try {
                $response = $this->runChain($request, $handler);
                $responseData = serialize($response);
                $cache->endDataCache($responseData);
            } catch (Throwable $e) {
                $cache->abortDataCache();
                throw $e;
            }
        }

        if (!($response instanceof ResponseInterface)) {
            throw new Exception('error create response');
        }

        return $response;
    }
}
