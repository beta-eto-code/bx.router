<?php


namespace BX\Router;


use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use BX\Router\Interfaces\ControllerInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplObjectStorage;

abstract class BaseController implements ControllerInterface
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
    protected $container;

    /**
     * @var SplObjectStorage
     */
    private $postData;

    public function setBitrixService(BitrixServiceInterface $bitrixService)
    {
        $this->bitrixService = $bitrixService;
    }

    public function setAppFactory(AppFactoryInterface $appFactory)
    {
        $this->appFactory = $appFactory;
    }

    public function setContainer(ContainerGetterInterface $containerGetter)
    {
        $this->container = $containerGetter;
    }

    /**
     * @param string $field
     * @param ServerRequestInterface $request
     * @return mixed|null
     */
    protected function getPostData(string $field, ServerRequestInterface $request)
    {
        $data = $this->getParsedPostData($request);
        return $data[$field] ?? null;
    }

    private function getParsedPostData(ServerRequestInterface $request): array
    {
        if (isset($this->postData[$request])) {
            return $this->postData[$request];
        }

        $this->postData = new SplObjectStorage();
        $data = json_decode($request->getBody()->getContents(), true);
        if ($data !== null) {
            return $this->postData[$request] = $data;
        }

        return $this->postData[$request] = $request->getParsedBody() ?? [];
    }
}
