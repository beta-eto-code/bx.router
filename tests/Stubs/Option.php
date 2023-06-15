<?php

namespace Bitrix\Main\Config;

class Option
{
    public static array $options = [];

    public static function get($moduleId, $name, $default = "", $siteId = false)
    {
        return static::$options[$moduleId][$name] ?? $default;
    }
}
