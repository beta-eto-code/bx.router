<?php

namespace Bitrix\Main;

class Context
{
    /**
     * @var HttpRequest
     */
    private $request;

    public function __construct()
    {
        $this->request = new HttpRequest();
    }

    public function getRequest()
    {
        return $this->request;
    }
}