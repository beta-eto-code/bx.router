<?php


namespace BX\Router;


use Bitrix\Main\Application;
use BX\Router\Interfaces\BitrixServiceInterface;

class BitrixService implements BitrixServiceInterface
{

    /**
     * @var Application|null
     */
    private $app;

    public function getBxApplication()
    {
        if ($this->app instanceof Application) {
            return $this->app;
        }

        return $this->app = Application::getInstance();
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
