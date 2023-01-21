<?php

namespace BX\Router\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\ORM\Data\DataManager;
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
     * @return void
     */
    public function includeModule(string $moduleName);

    /**
     * @return UserTable
     */
    public function getUserTable();

    /**
     * @param string $tableName
     * @return DataManager|null
     */
    public function getHlBlock(string $tableName);

    /**
     * @param string $className
     * @return DataManager
     */
    public function getTableByClass(string $className): string;

    /**
     * @param array $fileInfo
     * @param string $savePath
     * @return int
     */
    public function saveFile(array $fileInfo, string $savePath): int;

    /**
     * @param string $filePath
     * @return array
     */
    public function getFileInfo(string $filePath): array;

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
