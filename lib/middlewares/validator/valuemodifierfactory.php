<?php

namespace BX\Router\Middlewares\Validator;

class ValueModifierFactory
{
    public static function toInt(): callable
    {
        return function ($value): ?int {
            if (is_null($value)) {
                return null;
            }
            return (int)$value;
        };
    }

    public static function toFloat(): callable
    {
        return function ($value): ?float {
            if (is_null($value)) {
                return null;
            }
            return (float)$value;
        };
    }
}
