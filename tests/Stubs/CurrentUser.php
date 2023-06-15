<?php

namespace Bitrix\Main\Engine;

class CurrentUser
{
    private static ?CurrentUser $instance = null;
    private array $data;

    public static function initInstanceWithData(array $data): void
    {
        static::$instance = new CurrentUser($data);
    }

    public static function get(): CurrentUser
    {
        if (static::$instance instanceof CurrentUser) {
            return static::$instance;
        }

        return new CurrentUser(['id' => 1]);
    }

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getId(): int
    {
        return (int) ($this->data['id'] ?? 0);
    }
}
