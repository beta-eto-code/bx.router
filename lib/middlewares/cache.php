<?php


namespace BX\Router\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bitrix\Main\Data\Cache as BitrixCache;
use Throwable;

class Cache implements MiddlewareInterface
{
    const CACHE_DIR = 'router_cache';
    const ALLOW_METHOD = 'get';

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

        return 'query_'.md5(serialize($data));
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
                $response = $handler->handle($request);
                $responseData = serialize($response);
                $cache->endDataCache($responseData);
            } catch (Throwable $e) {
                $cache->abortDataCache();
                throw $e;
            }
        }

        return $response;
    }
}
