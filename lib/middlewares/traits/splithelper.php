<?php

namespace BX\Router\Middlewares\Traits;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait SplitHelper
{
    abstract protected function processRequest(ServerRequestInterface $request);
    abstract protected function processResponse(ResponseInterface $response): ResponseInterface;
    abstract protected function runChain(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->processRequest($request);
        $response = $this->runChain($request, $handler);

        return $this->processResponse($response);
    }
}
