<?php

namespace BX\Router\Middlewares\Validator;

class Factory
{
    private static ?RequestReader $requestReader = null;

    public static function getOrCreateRequestReader(): RequestReader
    {
        if (static::$requestReader instanceof RequestReader) {
            return static::$requestReader;
        }
        return static::$requestReader = new RequestReader();
    }
}
