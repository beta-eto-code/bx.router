<?php

namespace BX\Router;

use BX\Router\Interfaces\ComponentWrapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ComponentWrapper extends BaseController implements ComponentWrapperInterface
{
    private string $componentName;
    private string $templateName;
    private array $params;

    public function __construct(string $componentName, string $templateName = '', array $params = [])
    {
        $this->componentName = $componentName;
        $this->templateName = $templateName;
        $this->params = $params;
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
