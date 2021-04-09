<?php


namespace BX\Router;


use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProxyController extends BaseController
{
    /**
     * @var Closure
     */
    private $handleFunc;
    /**
     * @var RequestHandlerInterface
     */
    private $controller;

    /**
     * ProxyController constructor.
     * @param RequestHandlerInterface $controller
     * @param Closure $handleFunc
     */
    public function __construct(RequestHandlerInterface $controller, Closure $handleFunc)
    {
        $this->controller = $controller;
        $this->handleFunc = $handleFunc;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->controller->handle($request);
        return ($this->handleFunc)($request, $response);
    }
}