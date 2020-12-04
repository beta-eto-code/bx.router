<?php


namespace BX\Router;


use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;
use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\BitrixServiceInterface;
use BX\Router\Interfaces\ComponentWrapperInterface;
use BX\Router\PSR7\RequestAdapterPSR;
use BX\Router\PSR7\ResponseAdapterPSR;
use BX\Router\PSR7\ServerRequestAdapterPSR;
use BX\Router\PSR7\UploadedFile;
use BX\Router\PSR7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class AppFactory implements AppFactoryInterface
{
    /**
     * @var BitrixServiceInterface
     */
    private $bitrixService;

    public function __construct(BitrixServiceInterface $bitrixService)
    {
        $this->bitrixService = $bitrixService;
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        $bitrixRequest = new HttpRequest();
        $request = new RequestAdapterPSR($bitrixRequest);

        return $request->withMethod($method)->withUri($uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $resp = new HttpResponse();
        return (new ResponseAdapterPSR($resp))->withStatus($code, $reasonPhrase);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $bitrixRequest = new HttpRequest();
        $bitrixRequest->getServer()->setValues($serverParams);
        $request = new ServerRequestAdapterPSR($bitrixRequest);

        return $request->withMethod($method)->withUri($uri);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return stream_for($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return stream_for($filename);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return stream_for($resource);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }

    public function createComponentWrapper(string $componentName, string $templateName, array $params): ComponentWrapperInterface
    {
        // TODO: Implement createComponentWrapper() method.
    }
}
