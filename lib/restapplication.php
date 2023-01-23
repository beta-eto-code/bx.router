<?php

namespace BX\Router;

use Bitrix\Main\Application;
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
     * @psalm-suppress MissingDependency
     */
    private ExtendRouter $bitrixRouter;
    private Application $app;
    private Router $router;
    private ContainerInterface $container;
    private BitrixServiceInterface $bitrixService;
    private AppFactory $factory;
    private ResponseHandler $responseHandler;
    private ?MiddlewareChainInterface $middleware = null;

    public function __construct(ContainerInterface $container = null)
    {
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
     * @throws Exception
     * @psalm-suppress MissingDependency
     */
    public function run(): void
    {
        $this->initRoutes();
        $bitrixRequest = $this->app->getContext()->getRequest();
        /**
         * @psalm-suppress MissingDependency
         */
        $route = $this->bitrixRouter->match($bitrixRequest);
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

    private function initRoutes(): void
    {
        /**
         * @psalm-suppress MissingDependency,UndefinedMethod
         */
        $this->bitrixRouter->releaseRoutes(); // регистрируем внутренние роуты
    }

    /**
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
     * @param mixed $serviceInstance
     */
    public function setService(string $name, $serviceInstance): void
    {
        $this->container->set($name, $serviceInstance);
    }

    /**
     * @return mixed
     * @throws ObjectNotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function getService(string $name)
    {
        return $this->container->has($name) ? $this->container->get($name) : null;
    }

    public function getRouter(): RouterInterface
    {
        return $this->router;
    }

    public function setResponseHandler(ResponseHandler $responseHandler): void
    {
        $this->responseHandler = $responseHandler;
    }

    public function registerMiddleware(MiddlewareChainInterface $middleware): MiddlewareChainInterface
    {
        return $this->middleware = $middleware;
    }
}
