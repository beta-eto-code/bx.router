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
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $bitrixService;
    /**
     * @var AppFactoryInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $appFactory;
    /**
     * @var ContainerGetterInterface
     * @psalm-suppress PropertyNotSetInConstructor
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

    protected function getParsedPostData(ServerRequestInterface $request): array
    {
        if (isset($this->postData[$request])) {
            return $this->postData[$request];
        }

        $this->postData = new SplObjectStorage();
        $data = json_decode($request->getBody()->getContents(), true);
        if ($data !== null) {
            /**
             * @psalm-suppress InvalidArgument
             */
            return $this->postData[$request] = $data;
        }

        /**
         * @psalm-suppress InvalidArgument,InvalidReturnStatement
         */
        return $this->postData[$request] = $request->getParsedBody() ?? [];
    }
}
