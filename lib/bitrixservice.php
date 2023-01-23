<?php

namespace BX\Router;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use BX\Router\Interfaces\BitrixServiceInterface;
use CFile;
use CUser;

class BitrixService implements BitrixServiceInterface
{
    private ?Application $app = null;
    private ?CUser $cUser = null;
    private ?UserTable $userTable = null;
    private array $hlList = [];

    public function getBxApplication(): Application
    {
        if ($this->app instanceof Application) {
            return $this->app;
        }

        return $this->app = Application::getInstance();
    }

    /**
     * @param string $className
     * @return DataManager
     * @psalm-suppress MismatchingDocblockReturnType,InvalidReturnType,InvalidReturnStatement
     */
    public function getTableByClass(string $className): string
    {
        return $className;
    }

    public function getCUser(): CUser
    {
        if ($this->cUser instanceof CUser) {
            return $this->cUser;
        }

        return $this->cUser = new CUser();
    }

    public function getUserTable(): UserTable
    {
        if ($this->userTable instanceof UserTable) {
            return  $this->userTable;
        }

        return $this->userTable = new UserTable();
    }

    public function includeModule(string $moduleName): void
    {
        Loader::includeModule($moduleName);
    }

    /**
     * @param string $type
     * @param string $code
     * @return mixed|void
     * @psalm-suppress InvalidReturnType
     */
    public function getIblockElementManager(string $type, string $code)
    {
        // TODO: Implement getIblockElementManager() method.
    }

    /**
     * @param string $componentName
     * @param string $templateName
     * @param array $params
     * @param bool $returnResult
     * @return mixed|null
     */
    public function includeComponent(
        string $componentName,
        string $templateName = '',
        array $params = [],
        bool $returnResult = false
    ) {
        global $APPLICATION;

        ob_start();
        $result = $APPLICATION->IncludeComponent(
            $componentName,
            $templateName,
            $params,
            null,
            [],
            $returnResult
        );
        $data = ob_get_clean();

        return $returnResult ? $result : $data;
    }

    /**
     * @param string $tableName
     * @return DataManager|null
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getHlBlock(string $tableName)
    {
        if (!empty($this->hlList[$tableName])) {
            return $this->hlList[$tableName];
        }

        $this->includeModule('highloadblock');
        $hlData = HighloadBlockTable::getRow([
            'filter' => [
                '=TABLE_NAME' => $tableName,
            ]
        ]);

        return $this->hlList[$tableName] = HighloadBlockTable::compileEntity($hlData)->getDataClass();
    }

    /**
     * @param string $filePath
     * @return array
     */
    public function getFileInfo(string $filePath): array
    {
        return CFile::MakeFileArray($filePath);
    }

    /**
     * @param array $fileInfo
     * @param string $savePath
     * @return int
     */
    public function saveFile(array $fileInfo, string $savePath): int
    {
        return (int) CFile::SaveFile($fileInfo, $savePath);
    }
}
