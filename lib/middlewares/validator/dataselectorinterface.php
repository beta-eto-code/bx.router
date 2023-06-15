<?php

namespace BX\Router\Middlewares\Validator;

use Psr\Http\Message\ServerRequestInterface;

interface DataSelectorInterface
{
    public function getDataIterable(ServerRequestInterface $request): iterable;

    public static function getSubjectItemName(): string;
}
