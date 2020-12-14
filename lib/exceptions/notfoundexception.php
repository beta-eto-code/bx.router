<?php

namespace BX\Router\Exceptions;

use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends HttpException
{
    const PHRASE = 'Not found';
    const CODE = 404;

    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, static::CODE, static::PHRASE, $request, $appFactory);
    }
}
