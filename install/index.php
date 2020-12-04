<?

IncludeModuleLangFile(__FILE__);
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

    private function requireEntityFile(string $entityName)
    {
        $root = Application::getDocumentRoot();
        $pathFromBitrix = "{$root}/bitrix/modules/{$this->MODULE_ID}/lib/entities/".strtolower($entityName).'.php';
        if (file_exists($pathFromBitrix)) {
            require_once $pathFromBitrix;
            return;
        }

        $pathFromLocal = "{$root}/local/modules/{$this->MODULE_ID}/lib/entities/".strtolower($entityName).'.php';
        if (file_exists($pathFromLocal)) {
            require_once $pathFromLocal;
            return;
        }
    }

    public function DoInstall()
    {
        $this->requireEntityFile('RouterLogTable');
        RouterLogTable::getEntity()->createDbTable();
        ModuleManager::RegisterModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall()
    {
        $this->requireEntityFile('RouterLogTable');
        Application::getConnection()->dropTable(RouterLogTable::getTableName());
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }
}
