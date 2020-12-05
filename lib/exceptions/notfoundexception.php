<?php

namespace BX\Router\Exceptions;

use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends HttpException
{
    public function __construct(
        string $message,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ){
        parent::__construct($message, 404, 'Not found', $request, $appFactory);
    }
}
