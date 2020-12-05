<?php


namespace BX\Router\Middlewares;


use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\HttpExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpException implements MiddlewareInterface
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    public function __construct(AppFactoryInterface $appFactory)
    {
        $this->appFactory = $appFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpExceptionInterface $e) {
            $e->setRequest($request);
            return $e->getResponse($this->appFactory);
        }
    }
}
