<?php

namespace BX\Router;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProxyController extends BaseController
{
    private Closure $handleFunc;
    private RequestHandlerInterface $controller;

    public function __construct(RequestHandlerInterface $controller, Closure $handleFunc)
    {
        $this->controller = $controller;
        $this->handleFunc = $handleFunc;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->controller->handle($request);
        return ($this->handleFunc)($request, $response);
    }
}
