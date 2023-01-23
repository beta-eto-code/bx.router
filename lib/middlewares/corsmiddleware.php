<?php

namespace BX\Router\Middlewares;

use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareChainInterface
{
    use ChainHelper;

    private AppFactoryInterface $factory;
    /**
     * @var string[]
     */
    private array $allowOrigin;
    /**
     * @var string[]
     */
    private array $allowMethods;
    /**
     * @var string[]
     */
    private array $allowHeaders;

    public function __construct(
        AppFactoryInterface $factory,
        ?array $allowOrigin = null,
        ?array $allowMethods = null,
        ?array $allowHeaders = null
    ) {
        $this->factory = $factory;
        $this->allowOrigin = $allowOrigin ?? ['*'];
        $this->allowMethods = $allowMethods ?? ['PUT', 'GET', 'POST', 'DELETE', 'OPTIONS'];
        $this->allowHeaders = $allowHeaders ?? ['*'];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @psalm-suppress DocblockTypeContradiction,RedundantConditionGivenDocblockType
         */
        $origin = current($request->getHeader('Referer') ?? ($request->getHeader('Origin') ?? []));
        if (!empty($origin)) {
            $origin = trim($origin, '/');
        }

        if (empty($origin) || !in_array($origin, $this->allowOrigin)) {
            return $this->runChain($request, $handler);
        }

        $method = $request->getMethod();
        $request = $method !== 'OPTIONS' ? $this->runChain($request, $handler) :
            $this->factory->createResponse(204);

        return $request->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', $this->allowHeaders)
            ->withHeader('Access-Control-Allow-Methods', $this->allowMethods)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
