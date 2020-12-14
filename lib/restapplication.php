<?php

namespace BX\Router;

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Interfaces\RestAppInterface;
use BX\Router\Interfaces\RouterInterface;
use BX\Router\PSR7\ServerRequestAdapterPSR;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class RestApplication implements RestAppInterface
{
    /**
     * @var ExtendRouter
     */
    private $bitrixRouter;
    /**
     * @var Application|null
     */
    private $app;
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Container
     */
    private $container;
    /**
     * @var BitrixServiceInterface
     */
    private $bitrixService;
    /**
     * @var AppFactory
     */
    private $factory;
    /**
     * @var ResponseHandler
     */
    private $responseHandler;
    /**
     * @var MiddlewareChainInterface
     */
    private $middleware;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->bitrixRouter = new ExtendRouter;
        $this->router = new Router($this->app, $this->bitrixRouter);
        $this->responseHandler = new ResponseHandler();
        $this->container = new Container;
        $this->bitrixService = new BitrixService;
        $this->factory = new AppFactory($this->bitrixService, $this->container);
    }

    public function getBitrixService(): BitrixServiceInterface
    {
        return $this->bitrixService;
    }

    public function getFactory(): AppFactoryInterface
    {
        return $this->factory;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->bitrixRouter->releaseRoutes(); // регистрируем внутренние роуты
        $bitrixRequest = $this->app->getContext()->getRequest();
        $route = $this->bitrixRouter->match($this->app->getContext()->getRequest());

        /**
         * @var ControllerInterface|null $controller
         */
        $controller = $route->getController();
        if (empty($controller)) {
            return;
        }

        if (!($controller instanceof ControllerInterface)) {
            throw new Exception('Controller must implement BX\Router\Interfaces\ControllerInterface');
        }

        $controller->setBitrixService($this->bitrixService);    // пробрасываем сервисы битрикса в контроллер
        $controller->setAppFactory($this->factory);             // пробрасываем фабрику psr 17 в контроллер
        $controller->setContainer($this->container);            // пробрасываем контейнер (di - внешние сервисы) в контроллер

        $request = new ServerRequestAdapterPSR($bitrixRequest);
        foreach ($route->getParametersValues() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $response = $this->executeController($request, $controller);
        $this->responseHandler->handle($response);
        $this->bitrixService->getBxApplication()->terminate();
    }

    /**
     * @param ServerRequestInterface $request
     * @param ControllerInterface $controller
     * @return ResponseInterface|null
     */
    private function executeController(ServerRequestInterface $request, ControllerInterface $controller): ?ResponseInterface
    {
        $middleware = null;
        if ($this->middleware instanceof MiddlewareChainInterface) {
            $middleware = clone $this->middleware;
            $routerMiddleware = $this->bitrixRouter->getMiddlewaresByController($controller);
            if ($routerMiddleware instanceof MiddlewareChainInterface) {
                $middleware->addMiddleware($routerMiddleware);
            }
        } else {
            $middleware = $this->bitrixRouter->getMiddlewaresByController($controller);
        }

        if (empty($middleware)) {
            return $controller->handle($request);
        }

        return $middleware->process($request, $controller);
    }

    public function setService(string $name, $serviceInstance)
    {
        //TODO: replace with ContainerSetterInterface
        ServiceLocator::getInstance()->addInstance($name, $serviceInstance);
    }

    public function getRouter(): RouterInterface
    {
        return $this->router;
    }

    /**
     * @param ResponseHandler $responseHandler
     * @return void
     */
    public function setResponseHandler(ResponseHandler $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * @param MiddlewareChainInterface $middleware
     * @return MiddlewareChainInterface
     */
    public function registerMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface
    {
        return $this->middleware = $middleware;
    }
}
