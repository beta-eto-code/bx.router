<?php

namespace BX\Router\Middlewares\Validator;

use Psr\Http\Message\ServerRequestInterface;

class HeaderSelector implements DataSelectorInterface
{
    private array $headerNames;

    public function __construct(string ...$headerNames)
    {
        $this->headerNames = $headerNames;
    }

    public function getDataIterable(ServerRequestInterface $request): iterable
    {
        $result = [];
        $headerList = $request->getHeaders();
        foreach ($this->headerNames as $headerName) {
            $value = $headerList[$headerName] ?? null;
            if (is_array($value) && count($value) === 1) {
                $value = current($value);
            }

            $result[$headerName] = $value;
        }

        return $result;
    }

    public static function getSubjectItemName(): string
    {
        return 'заголовок';
    }
}
