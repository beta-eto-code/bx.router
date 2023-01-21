<?php

namespace BX\Router\Middlewares;

use BX\Router\Exceptions\FormException;
use Psr\Http\Message\ServerRequestInterface;

interface ValidatorDataInterface
{
    public function __construct(DataSelectorInterface ...$selectorList);

    /**
     * @throws FormException
     */
    public function validate(ServerRequestInterface $request): void;
}
