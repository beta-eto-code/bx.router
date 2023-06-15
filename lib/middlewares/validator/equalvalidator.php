<?php

namespace BX\Router\Middlewares\Validator;

use Exception;

class EqualValidator extends BaseValidator
{
    private array $values;
    private bool $isStrictMode;

    public static function fromBody(array $values, string ...$fieldNames): EqualValidator
    {
        return new EqualValidator(
            $values,
            false,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    public static function fromHeaders(array $values, string ...$headerNames): EqualValidator
    {
        return new EqualValidator(
            $values,
            false,
            new HeaderSelector(...$headerNames)
        );
    }

    public static function fromAttributes(array $values, string ...$attributeNames): EqualValidator
    {
        return new EqualValidator(
            $values,
            false,
            new AttributeSelector(...$attributeNames)
        );
    }

    public function __construct(array $values, bool $isStrictMode, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->isStrictMode = $isStrictMode;
        $this->values = $values;
    }

    public function withStrictMode(): EqualValidator
    {
        $newValidator = clone $this;
        $newValidator->isStrictMode = true;
        return $newValidator;
    }

    /**
     * @throws Exception
     */
    protected function validateItem(SelectorItem $item): void
    {
        if (is_null($item->value)) {
            return;
        }

        if (!in_array($item->value, $this->values, $this->isStrictMode)) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }
    }
}
