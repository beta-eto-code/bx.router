<?php


namespace BX\Router\Exceptions;


use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerErrorException extends HttpException
{
    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, 500, 'Server error', $request, $appFactory);
    }
}
