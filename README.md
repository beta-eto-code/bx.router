# Роутер для bitrix (PSR-7, PSR-15, PSR-17 implementation)

### Установка

```
composer require beta/bx.router
```

### Пример инициализации приложения:

```php
use BX\Router\RestApplication;
use BX\Router\Middlewares\Logger;


$app = new RestApplication();
$router = $app->getRouter();

$app->setResponseHandler(new CustomResponseHandler);    // Устанавливаем собственный обработчик ответа
$app->setService('jwt', new UserTokenService());        // Регистрируем внешний сервис для доступа из контроллера

$logger = new Logger();
$router->get('/api/v1/catalog/', new CatalogController)->registerMiddleware($logger);
$router->get('/api/v1/some/{test}/', new SomeController)->registerMiddleware($logger);
$router->get('/api/v1/pages/main/', new MainPageConroller)
    ->useCache(3600, 'main_page')       // Кешируем ответ, ключ не обязателен, работает только c GET методами
    ->registerMiddleware($logger);
$router->default(new DefaultController) // Контроллер по-умолчанию
    ->registerMiddleware($logger); 

$app->run();
```

### Пример контроллера c вызовом компонента:

```php
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use BX\Router\BaseController;

class CatalogController extends BaseController
{
    /**
    * @var BitrixServiceInterface
     */
    protected $bitrixService;
    /**
    * @var AppFactoryInterface
     */
    protected $appFactory;
    /**
    * @var ContainerGetterInterface
     */
    protected $containerGetter;
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $component = $this->appFactory->createComponentWrapper('api:catlog.list'); // Создаем обертку компонента
        $component->setContainer($this->containerGetter);
        $component->setAppFactory($this->appFactory);
        $component->setBitrixService($this->bitrixService);
        
        return $component->handle($request);    // Возвращаем ответ с данными из массива $arResult
    }
}
```

### Пример простого контроллера:

```php
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use BX\Router\BaseController;

class MainPageConroller extends BaseController
{
    /**
    * @var BitrixServiceInterface
     */
    protected $bitrixService;
    /**
    * @var AppFactoryInterface
     */
    protected $appFactory;
    /**
    * @var ContainerGetterInterface
     */
    protected $containerGetter;
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->containerGetter->has('jwt'); // проверяем внешний сервис
        $jwt = $this->containerGetter->get('jwt');
        if (!($jwt instanceof UserTokenService)) {
            throw new \Exception('Что-то пошло не так...');
        }

        $request->getAttribute('test'); // атрибут из адресной строки
        $request->getAttributes();      // список атрибутов из адресной строки
        
        $jwtToken = trim(str_replace('Bearer', $request->getHeader('Authorization')));
        $userContext = $jwt->getUserContext($jwtToken);
        $user = $userContext->getUser();
        
        $data = $user->toArray();
        $response = $this->appFactory->createResponse();
        $response->getBody()->write(json_encode($data));
        
        return $response;
    }
}
```

### Пример контроллера с выборкой атрибутов из адресной строки:

```php 
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use BX\Router\BaseController;

class SomeController extends BaseController
{
    /**
    * @var BitrixServiceInterface
     */
    protected $bitrixService;
    /**
    * @var AppFactoryInterface
     */
    protected $appFactory;
    /**
    * @var ContainerGetterInterface
     */
    protected $containerGetter;
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $testAttribute = $request->getAttribute('test'); // атрибут из адресной строки
        $attributes = $request->getAttributes();         // список атрибутов из адресной строки
        
        $response = $this->appFactory->createResponse();
        $response->getBody()->write(json_encode($attributes));
        
        return $response;
    }
}
```
