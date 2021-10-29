<?php


namespace BX\Router;


use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;
use BitrixPSR17\HttpFactory;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ComponentWrapperInterface;
use BX\Router\Interfaces\ContainerGetterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use const UPLOAD_ERR_OK;

class AppFactory implements AppFactoryInterface
{
    /**
     * @var BitrixServiceInterface
     */
    private $bitrixService;
    /**
     * @var ContainerGetterInterface
     */
    private $containerGetter;
    /**
     * @var HttpFactory
     */
    private $httpFactory;

    public function __construct(BitrixServiceInterface $bitrixService, ContainerGetterInterface $containerGetter)
    {
        $this->bitrixService = $bitrixService;
        $this->httpFactory = new HttpFactory();
        $this->containerGetter = $containerGetter;
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->httpFactory->createRequest($method, $uri);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->httpFactory->createResponse($code, $reasonPhrase);
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array $serverParams
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->httpFactory->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * @param string $content
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return $this->httpFactory->createStream($content);
    }

    /**
     * @param string $filename
     * @param string $mode
     * @return StreamInterface
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->httpFactory->createStreamFromFile($filename, $mode);
    }

    /**
     * @param resource $resource
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return $this->httpFactory->createStreamFromResource($resource);
    }

    /**
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @return UploadedFileInterface
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return $this->httpFactory->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * @param string $uri
     * @return UriInterface
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return $this->createUri($uri);
    }

    /**
     * @param string $componentName
     * @param string $templateName
     * @param array $params
     * @return ComponentWrapperInterface
     */
    public function createComponentWrapper(
        string $componentName,
        string $templateName = '',
        array $params = []
    ): ComponentWrapperInterface
    {
        $wrapper = new ComponentWrapper($componentName, $templateName, $params);
        $wrapper->setAppFactory($this);
        $wrapper->setContainer($this->containerGetter);
        $wrapper->setBitrixService($this->bitrixService);

        return $wrapper;
    }

    /**
     * @param array $data
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public function createJsonResponse(
        array $data,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface
    {
        $response = $this->createResponse($code, $reasonPhrase);
        $response->getBody()->write(json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        ));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
