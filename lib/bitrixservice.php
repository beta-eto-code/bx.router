<?php


namespace BX\Router;


use Bitrix\Main\Application;
use Bitrix\Main\UserTable;
use BX\Router\Interfaces\BitrixServiceInterface;
use CUser;

class BitrixService implements BitrixServiceInterface
{

    /**
     * @var Application|null
     */
    private $app;
    /**
     * @var CUser
     */
    private $cUser;
    /**
     * @var UserTable
     */
    private $userTable;

    public function getBxApplication()
    {
        if ($this->app instanceof Application) {
            return $this->app;
        }

        return $this->app = Application::getInstance();
    }

    /**
     * @return CUser
     */
    public function getCUser()
    {
        if ($this->cUser instanceof CUser) {
            return $this->cUser;
        }

        return $this->cUser = new CUser();
    }

    /**
     * @return UserTable
     */
    public function getUserTable()
    {
        if ($this->userTable instanceof UserTable) {
            return  $this->userTable;
        }

        return $this->userTable = new UserTable();
    }

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
    )
    {
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
}
