<?php

namespace BX\Router\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

class BodyDataSelector implements DataSelectorInterface
{
    /**
     * @var string[]
     */
    private array $fieldNames;

    public function __construct(string ...$fieldNames)
    {
        $this->fieldNames = $fieldNames;
    }

    public function getDataIterable(ServerRequestInterface $request): iterable
    {
        return [];
    }
}
