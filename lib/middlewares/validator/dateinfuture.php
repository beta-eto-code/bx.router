<?php

namespace BX\Router\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

class DateInFuture implements ValidatorDataInterface
{
    private string $dateFormat;
    /**
     * @var DataSelectorInterface[]
     */
    private array $selectorList;

    public static function fromBody(string $dateFormat, string ...$fieldNames): DateInFuture
    {
        return new DateInFuture($dateFormat, new BodyDataSelector(...$fieldNames));
    }

    public function __construct(string $dateFormat, DataSelectorInterface ...$selectorList)
    {
        $this->dateFormat = $dateFormat;
        $this->selectorList = $selectorList;
    }

    public function validate(ServerRequestInterface $request): void
    {
        // TODO: Implement validate() method.
    }
}
