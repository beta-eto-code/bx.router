<?php

namespace BX\Router\Middlewares\Validator;

use Exception;

class RegexpValidator extends BaseValidator
{
    private string $pattern;

    public static function fromBody(string $pattern, string ...$fieldNames): RegexpValidator
    {
        return new RegexpValidator(
            $pattern,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }


    public static function fromHeaders(string $pattern, string ...$headerNames): RegexpValidator
    {
        return new RegexpValidator(
            $pattern,
            new HeaderSelector(...$headerNames)
        );
    }

    public static function fromAttributes(string $pattern, string ...$attributeNames): RegexpValidator
    {
        return new RegexpValidator(
            $pattern,
            new AttributeSelector(...$attributeNames)
        );
    }

    public function __construct(string $pattern, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->pattern = $pattern;
    }

    /**
     * @throws Exception
     */
    protected function validateItem(SelectorItem $item): void
    {
        if (is_null($item->value)) {
            return;
        }

        if (empty(preg_match($this->pattern, $item->value))) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }
    }
}
