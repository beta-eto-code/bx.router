<?php

namespace BX\Router\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\UserTable;
use CUser;

interface BitrixServiceInterface
{
    public function getBxApplication(): Application;

    /**
     * @psalm-suppress UndefinedDocblockClass
     */
    public function getCUser(): CUser;

    public function includeModule(string $moduleName): void;

    /**
     * @return UserTable
     */
    public function getUserTable(): UserTable;

    /**
     * @param string $tableName
     * @return DataManager|null
     */
    public function getHlBlock(string $tableName);

    /**
     * @param string $className
     * @return DataManager
     * @psalm-suppress MismatchingDocblockReturnType
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
