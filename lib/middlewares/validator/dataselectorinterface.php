<?php

namespace BX\Router\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

interface DataSelectorInterface
{
    public function getDataIterable(ServerRequestInterface $request): iterable;
}
