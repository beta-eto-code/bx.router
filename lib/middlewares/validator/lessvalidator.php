<?php

namespace BX\Router\Middlewares\Validator;

use Exception;

class LessValidator extends BaseValidator
{
    /**
     * @var mixed
     */
    private $toValue;
    private bool $equalFlag;

    /**
     * @param mixed $toValue
     * @param string ...$fieldNames
     * @return LessValidator
     */
    public static function fromBody($toValue, string ...$fieldNames): LessValidator
    {
        return new LessValidator(
            $toValue,
            false,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    /**
     * @param mixed $toValue
     * @param string ...$headerNames
     * @return LessValidator
     */
    public static function fromHeaders($toValue, string ...$headerNames): LessValidator
    {
        return new LessValidator(
            $toValue,
            false,
            new HeaderSelector(...$headerNames)
        );
    }

    /**
     * @param mixed $toValue
     * @param string ...$attributeNames
     * @return LessValidator
     */
    public static function fromAttributes($toValue, string ...$attributeNames): LessValidator
    {
        return new LessValidator(
            $toValue,
            false,
            new AttributeSelector(...$attributeNames)
        );
    }

    /**
     * @param mixed $toValue
     * @param DataSelectorInterface ...$selectorList
     */
    public function __construct($toValue, bool $equalFlag, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->toValue = $toValue;
        $this->equalFlag = $equalFlag;
    }

    public function withEqual(): LessValidator
    {
        $newValidator = clone $this;
        $newValidator->equalFlag = true;
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

        if ($this->equalFlag && $this->toValue < $item->value) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }

        if (!$this->equalFlag && $this->toValue <= $item->value) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }
    }
}
