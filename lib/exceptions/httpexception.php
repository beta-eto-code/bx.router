<?php


namespace BX\Router\Exceptions;


use BX\Router\Interfaces\AppFactoryInterface;
use BX\Router\Interfaces\HttpExceptionInterface;
use Matrix\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpException extends Exception implements HttpExceptionInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var string
     */
    private $phrase;
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    public function __construct(
        string $message,
        int $code,
        string $phrase,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    )
    {
        $this->request = $request;
        $this->phrase = $phrase;
        $this->appFactory = $appFactory;
        parent::__construct($message, $code);
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getResponse(AppFactoryInterface $appFactory = null): ?ResponseInterface
    {
        $appFactory = $appFactory ?? $this->appFactory;
        if (empty($appFactory)) {
            return null;
        }

        $response = $appFactory->createResponse((int)$this->getCode(), $this->phrase);
        $response->getBody()->write(json_encode([
            'error' => true,
            'errorMessage' => $this->getMessage()
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}
