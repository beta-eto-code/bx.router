<?php

namespace BX\Router;

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\ObjectNotFoundException;
use BitrixPSR7\ServerRequest;
use BX\Router\Bitrix\ExtendRouter;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ContainerInterface;
use BX\Router\Interfaces\ControllerInterface;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Interfaces\RestAppInterface;
use BX\Router\Interfaces\RouterInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class RestApplication implements RestAppInterface
{
    /**
     * @var ExtendRouter
     * @psalm-suppress MissingDependency
     */
    private $bitrixRouter;
    /**
     * @var Application
     */
    private $app;
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ContainerInterface
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
     * @var MiddlewareChainInterface|null
     */
    private $middleware;

    public function __construct(ContainerInterface $container = null)
    {
        /**
         * @psalm-suppress MissingDependency
         */
        $this->app = Application::getInstance();
        /**
         * @psalm-suppress MissingDependency
         */
        $this->bitrixRouter = new ExtendRouter();
        $this->router = new Router($this->app, $this->bitrixRouter);
        $this->responseHandler = new ResponseHandler();
        /**
         * @psalm-suppress PossiblyInvalidPropertyAssignmentValue
         */
        $this->container = $container ?? new Container();
        $this->bitrixService = new BitrixService();
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
     * @return void
     * @throws Exception
     * @psalm-suppress MissingDependency
     */
    public function run()
    {
        $this->initRoutes();
        $bitrixRequest = $this->app->getContext()->getRequest();
        /**
         * @psalm-suppress MissingDependency
         */
        $route = $this->bitrixRouter->match($this->app->getContext()->getRequest());
        if (empty($route)) {
            return;
        }

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

        $controller->setBitrixService($this->bitrixService); // пробрасываем сервисы битрикса в контроллер
        $controller->setAppFactory($this->factory); // пробрасываем фабрику psr 17 в контроллер
        $controller->setContainer($this->container); // пробрасываем контейнер (di - внешние сервисы) в контроллер

        $request = new ServerRequest($bitrixRequest);
        foreach ($route->getParametersValues() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $response = $this->executeController($request, $controller);
        if (empty($response)) {
            return;
        }

        $this->responseHandler->handle($response);
        $this->bitrixService->getBxApplication()->terminate();
    }

    /**
     * @return void
     */
    private function initRoutes()
    {
        /**
         * @psalm-suppress MissingDependency,UndefinedMethod
         */
        $this->bitrixRouter->releaseRoutes(); // регистрируем внутренние роуты
    }

    /**
     * @param ServerRequestInterface $request
     * @param ControllerInterface $controller
     * @return ResponseInterface|null
     * @psalm-suppress LessSpecificReturnType
     */
    private function executeController(
        ServerRequestInterface $request,
        ControllerInterface $controller
    ): ?ResponseInterface {
        if ($this->middleware instanceof MiddlewareChainInterface) {
            $middleware = clone $this->middleware;
            /**
             * @psalm-suppress MissingDependency
             */
            $routerMiddleware = $this->bitrixRouter->getMiddlewaresByController($controller);
            if ($routerMiddleware instanceof MiddlewareChainInterface) {
                $middleware->addMiddleware($routerMiddleware);
            }
        } else {
            /**
             * @psalm-suppress MissingDependency
             */
            $middleware = $this->bitrixRouter->getMiddlewaresByController($controller);
        }

        if (empty($middleware)) {
            return $controller->handle($request);
        }

        return $middleware->process($request, $controller);
    }

    /**
     * @param string $name
     * @param mixed $serviceInstance
     * @return void
     */
    public function setService(string $name, $serviceInstance)
    {
        $this->container->set($name, $serviceInstance);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ObjectNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function getService(string $name)
    {
        return $this->container->has($name) ? $this->container->get($name) : null;
    }

    /**
     * @return RouterInterface
     */
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
