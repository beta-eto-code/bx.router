<?php

namespace BX\Router;

use BX\Router\Interfaces\ComponentWrapperInterface;
use CBitrixComponent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class ComponentWrapper extends BaseController implements ComponentWrapperInterface
{
    /**
     * @var string
     */
    private $componentName;
    /**
     * @var string
     */
    private $templateName;
    /**
     * @var array
     */
    private $params;

    public function __construct(string $componentName, string $templateName = '', array $params = [])
    {
        $this->componentName = $componentName;
        $this->templateName = $templateName;
        $this->params = $params;
    }

    /**
     * Получаем имя класса компонента
     *
     * @param string $componentName
     * @return string
     */
    private function getComponentClass(string $componentName): string
    {
        $component = new CBitrixComponent();
        $component->initComponent($componentName);
        $reflectionComponent = new ReflectionClass($component);
        $classOfComponent = $reflectionComponent->getProperty('classOfComponent');
        $classOfComponent->setAccessible(true);

        return (string)$classOfComponent->getValue();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->appFactory
            ->createResponse()
            ->withHeader('Content-Type', 'application/json');

        $params = $this->params;
        $params['bitrixService'] = $this->bitrixService;
        $params['appFactory'] = $this->appFactory;
        $params['container'] = $this->container;
        $params['request'] = $request;

        $data = $this->bitrixService->includeComponent(
            $this->componentName,
            $this->templateName,
            $params,
            true
        );

        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $response->getBody()->write((string)$data);

        return $response;
    }
}
