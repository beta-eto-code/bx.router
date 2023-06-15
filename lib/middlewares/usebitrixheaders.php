<?php

namespace BX\Router\Middlewares;

use Bitrix\Main\Context;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UseBitrixHeaders implements MiddlewareChainInterface
{
    use ChainHelper;


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->runChain($request, $handler);
        /**
         * @psalm-suppress UndefinedMethod
         */
        $bxResponse = Context::getCurrent()->getResponse();
        $bxResponse->writeHeaders();

        return $response;
    }
}
