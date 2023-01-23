<?php

namespace Bitrix\Main\Routing;

class Options
{
    /**
     * @var string[]
     */
    private array $methods;

    public function methods(array $methods): void
    {
        $this->methods = $methods;
    }
}
