<?php


namespace BX\Router\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerInterface
{
    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function handle(ResponseInterface $response);
}
