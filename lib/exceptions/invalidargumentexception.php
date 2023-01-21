<?php

namespace BX\Router\Exceptions;

use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class InvalidArgumentException extends HttpException
{
    public const PHRASE = 'Invalid argument';
    public const CODE = 400;

    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ) {
        parent::__construct($message, static::CODE, static::PHRASE, $request, $appFactory);
    }
}
