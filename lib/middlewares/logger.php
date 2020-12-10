<?php

namespace BX\Router\Middlewares;

use Bitrix\Main\Type\DateTime;
use BX\Router\Entities\RouterLogTable;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Logger implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->runChain($request, $handler);
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
