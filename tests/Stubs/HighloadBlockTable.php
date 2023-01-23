<?php

namespace Bitrix\Highloadblock;

use Bitrix\Main\ORM\Entity;

class HighloadBlockTable
{
    /**
     * @param array $params
     * @return mixed
     */
    public static function getRow(array $params)
    {
        return false;
    }

    /**
     * @param mixed $hlData
     * @return Entity
     */
    public static function compileEntity($hlData)
    {
        return new Entity();
    }
}
