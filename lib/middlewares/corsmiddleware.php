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
        $origin = $this->getOriginFromRequest($request);
        if (!$this->originIsValid($origin) || !$this->methodIsValid($request)) {
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

    private function methodIsValid(ServerRequestInterface $request): bool
    {
        $currentMethod = strtoupper($request->getMethod());
        return $currentMethod === 'OPTIONS' || in_array($currentMethod, $this->getAllowedMethodWithUpperCase());
    }

    private function getAllowedMethodWithUpperCase(): array
    {
        return array_map(function ($method): string {
            return strtoupper((string) $method);
        }, $this->allowMethods);
    }

    private function originIsValid(string $origin): bool
    {
        return !empty($origin) && in_array($origin, $this->allowOrigin);
    }

    private function getOriginFromRequest(ServerRequestInterface $request): string
    {
        $origin = current($request->getHeader('Origin') ?: ($request->getHeader('Referer') ?? []));
        if (!empty($origin)) {
            $origin = trim($origin, '/');
        }
        return $origin ?: '';
    }
}
