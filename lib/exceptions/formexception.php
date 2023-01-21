<?php

namespace BX\Router\Exceptions;

use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FormException extends HttpException
{
    public const PHRASE = 'Invalid form data';
    public const CODE = 400;

    /**
     * @var array
     */
    private $errors;

    public function __construct(
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    ) {
        $this->errors = [];
        parent::__construct('', static::CODE, static::PHRASE, $request, $appFactory);
    }

    /**
     * @param string $fieldName
     * @param string $message
     * @return $this
     */
    public function addErrorField(string $fieldName, string $message): self
    {
        $this->errors[$fieldName] = $message;
        return $this;
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
            'form' => $this->errors
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
