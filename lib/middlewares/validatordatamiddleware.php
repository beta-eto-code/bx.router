<?php

namespace BX\Router\Middlewares;

use BX\Router\Exceptions\FormException;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidatorDataMiddleware implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @var ValidatorDataInterface[]
     */
    private array $validatorList;

    public function __construct(ValidatorDataInterface ...$validatorList)
    {
        $this->validatorList = $validatorList;
    }

    /**
     * @throws FormException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach ($this->validatorList as $dataValidator) {
            $dataValidator->validate($request);
        }

        return $handler->handle($request);
    }
}
