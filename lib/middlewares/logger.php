<?php

namespace BX\Router\Middlewares;

use Bitrix\Main\Type\DateTime;
use BX\Router\Entities\RouterLogTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Logger implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        RouterLogTable::add([
            'url' => $request->getRequestTarget(),
            'method' => $request->getMethod(),
            'controller' => get_class($handler),
            'request_body' => (string)$request->getBody(),
            'response_body' => (string)$response->getBody(),
            'request_headers' => json_encode($request->getHeaders()),
            'response_headers' => json_encode($response->getHeaders()),
            'date_create' => new DateTime(),
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
