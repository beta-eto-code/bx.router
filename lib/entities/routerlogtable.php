<?php

namespace BX\Router\Entities;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Type\DateTime;

class RouterLogTable extends DataManager
{
    public static function getTableName()
    {
        return 'router_log';
    }

    public static function getMap()
    {
        return [
            'id' => new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            'url' => new StringField('url', ['size' => 400]),
            'method' => new StringField('method', ['size' => 10]),
            'controller' => new StringField('controller', ['size' => 255]),
            'request_body' => new TextField('request_body'),
            'response_body' => new TextField('response_body'),
            'request_headers' => new TextField('request_headers'),
            'response_headers' => new TextField('response_headers'),
            'status' => new IntegerField('status'),
            'date_create' => new DatetimeField('date_create', ['default_value' => new DateTime()]),
        ];
    }
}
