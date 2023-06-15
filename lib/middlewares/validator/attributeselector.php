<?php

namespace BX\Router\Middlewares\Validator;

use Psr\Http\Message\ServerRequestInterface;

class AttributeSelector implements DataSelectorInterface
{
    /**
     * @var string[]
     */
    private array $attributeNames;

    public function __construct(string ...$attributeNames)
    {
        $this->attributeNames = $attributeNames;
    }

    public function getDataIterable(ServerRequestInterface $request): iterable
    {
        $result = [];
        $attributeList = $request->getAttributes();
        foreach ($this->attributeNames as $attributeName) {
            $result[$attributeName] = $attributeList[$attributeName] ?? null;
        }
        return $result;
    }

    public static function getSubjectItemName(): string
    {
        return 'аттрибут';
    }
}
