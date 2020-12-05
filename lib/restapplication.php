<?php

namespace BX\Router;

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\RestAppInterface;
use BX\Router\Interfaces\RouterInterface;
use BX\Router\PSR7\ServerRequestAdapterPSR;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @var MiddlewareInterface[]
     */
    private $middlewares;

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

    public function getFactory(): AppFactoryInterface
    {
        return $this->factory;
    }

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
            new \Exception('Controller must implement BX\Router\Interfaces\ControllerInterface');
        }

        $controller->setBitrixService($this->bitrixService);    // пробрасываем сервисы битрикса в контроллер
        $controller->setAppFactory($this->factory);             // пробрасываем фабрику psr 17 в контроллер
        $controller->setContainer($this->container);            // пробрасываем контейнер (di - внешние сервисы) в контроллер

        $request = new ServerRequestAdapterPSR($bitrixRequest);
        foreach ($route->getParametersValues() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $response = null;
        $newController = $this->makeLazyController($controller);
        foreach ($this->middlewares as $middleware) {
            $response = $middleware->process($request, $newController);
            $newController = $this->getNewHandler($response);
        }

        if (empty($response)) {
            $response = $newController->handle($request);
        }

        $this->responseHandler->handle($response);
        $this->bitrixService->getBxApplication()->terminate();
    }

    /**
     * @param ControllerInterface $controller
     * @return RequestHandlerInterface
     */
    private function makeLazyController(ControllerInterface $controller)
    {
        $call = function (ServerRequestInterface $request, ControllerInterface $controller) {
            return $this->executeController($request, $controller);
        };

        return new class($call, $controller) implements RequestHandlerInterface {

            /**
             * @var callable
             */
            private $call;

            /**
             * @var ControllerInterface
             */
            private $controller;

            public function __construct(
                callable $call,
                ControllerInterface $controller
            ){
                $this->call = $call;
                $this->controller = $controller;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $call = $this->call;
                return $call($request, $this->controller);
            }
        };
    }

    /**
     * @param ServerRequestInterface $request
     * @param ControllerInterface $controller
     * @return ResponseInterface|null
     */
    private function executeController(ServerRequestInterface $request, ControllerInterface $controller): ?ResponseInterface
    {
        $middlewares = $this->bitrixRouter->getMiddlewaresByController($controller);
        if (empty($middlewares)) {
            return $controller->handle($request);
        }

        $response = null;
        foreach ($middlewares as $key => $middleware) {
            $response = $middleware->process($request, $controller);
            $controller = $this->getNewHandler($response);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return RequestHandlerInterface
     */
    private function getNewHandler(ResponseInterface $response): RequestHandlerInterface
    {
        return new class($response) implements RequestHandlerInterface {
            private $response;

            public function __construct(ResponseInterface $response) {
                $this->response = $response;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };
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
     * @param MiddlewareInterface $middleware
     * @return void
     */
    public function registerMiddleware(MiddlewareInterface $middleware)
    {
        $middlewareClass = get_class($middleware);
        $this->middlewares[$middlewareClass] = $middleware;
    }
}
