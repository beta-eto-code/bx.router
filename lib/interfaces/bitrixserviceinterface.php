<?php


namespace BX\Router\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\UserTable;
use CUser;

interface BitrixServiceInterface
{
    /**
     * @return Application
     */
    public function getBxApplication();

    /**
     * @return CUser
     */
    public function getCUser();

    /**
     * @return UserTable
     */
    public function getUserTable();

    /**
     * @param string $componentName
     * @param string $templateName
     * @param array $params
     * @param bool $returnResult
     * @return mixed
     */
    public function includeComponent(
        string $componentName,
        string $templateName = '',
        array $params = [],
        bool $returnResult = false
    );

    /**
     * @param string $type
     * @param string $code
     * @return mixed
     */
    public function getIblockElementManager(string $type, string $code);
}
