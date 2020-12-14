<?php


namespace BX\Router\Exceptions;


use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerErrorException extends HttpException
{
    const PHRASE = 'Server error';
    const CODE = 500;

    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, static::CODE, static::PHRASE, $request, $appFactory);
    }
}
