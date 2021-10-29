<?php


namespace BX\Router\Interfaces;


use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

interface AppFactoryInterface extends
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    /**
     * @param string $componentName
     * @param string $templateName
     * @param array $params
     * @return ComponentWrapperInterface
     */
    public function createComponentWrapper(
        string $componentName,
        string $templateName = '',
        array $params = []): ComponentWrapperInterface;

    /**
     * @param array $data
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public function makeJsonResponse(
        array $data,
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface;
}
