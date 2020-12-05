<?php


namespace BX\Router\Exceptions;


use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ForbiddenException extends HttpException
{
    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, 403, 'Forbidden', $request, $appFactory);
    }
}
