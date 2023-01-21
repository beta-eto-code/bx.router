<?php

namespace BX\Router\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

class DateInFuture implements ValidatorDataInterface
{
    public function __construct(DataSelectorInterface ...$selectorList)
    {
    }

    public function validate(ServerRequestInterface $request): void
    {
        // TODO: Implement validate() method.
    }
}
