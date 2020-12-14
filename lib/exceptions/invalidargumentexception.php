<?php


namespace BX\Router\Exceptions;


use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class InvalidArgumentException extends HttpException
{
    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, 400, 'Invalid argument', $request, $appFactory);
    }
}
