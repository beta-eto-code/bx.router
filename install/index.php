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
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2020-11-28 22:03:00";
        $this->MODULE_NAME = "Роутер";
        $this->MODULE_DESCRIPTION = "Роутер";
    }

    public function DoInstall()
    {
        ModuleManager::RegisterModule($this->MODULE_ID);
        $this->InstallDB();
        return true;
    }

    public function InstallDB()
    {
        if(!Loader::includeModule($this->MODULE_ID)){
            return false;
        }
        RouterLogTable::getEntity()->createDbTable();
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
