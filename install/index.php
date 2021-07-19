<?

IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use BX\Router\Entities\RouterLogTable;
use Bitrix\Main\Application;

class bx_router extends CModule
{
    public $MODULE_ID = "bx.router";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $errors;

    public function __construct()
    {
        $this->MODULE_VERSION = "1.0.1";
        $this->MODULE_VERSION_DATE = "2020-11-28 22:03:00";
        $this->MODULE_NAME = "Роутер";
        $this->MODULE_DESCRIPTION = "Роутер";
    }

    /**
     * @param string $message
     */
    public function setError(string $message)
    {
        $GLOBALS["APPLICATION"]->ThrowException($message);
    }

    /**
     * @return bool
     */
    public function installRequiredModules(): bool
    {
        $isInstalled = ModuleManager::isModuleInstalled('bx.jwt');
        if ($isInstalled) {
            return true;
        }

        $modulePath = getLocalPath("modules/bx.jwt/install/index.php");
        if (!$modulePath) {
            $this->setError('Отсутствует модуль bx.jwt - https://github.com/beta-eto-code/bx.jwt');
            return false;
        }

        require_once $_SERVER['DOCUMENT_ROOT'].$modulePath;
        $moduleInstaller = new bx_jwt();
        $resultInstall = (bool)$moduleInstaller->DoInstall();
        if (!$resultInstall) {
            $this->setError('Ошибка установки модуля bx.jwt');
        }

        return $resultInstall;
    }

    public function DoInstall(): bool
    {
        $result = $this->installRequiredModules();
        if (!$result) {
            return false;
        }

        ModuleManager::RegisterModule($this->MODULE_ID);
        $this->InstallDB();
        return true;
    }

    public function InstallDB(): bool
    {
        if(!Loader::includeModule($this->MODULE_ID)){
            return false;
        }
        RouterLogTable::getEntity()->createDbTable();
        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function UnInstallDB()
    {
        if(!Loader::includeModule($this->MODULE_ID)){
            return false;
        }
        Application::getConnection()->dropTable(RouterLogTable::getTableName());
    }
}
