<?php

namespace BX\Router\Middlewares\Validator;

use Exception;

class MoreValidator extends BaseValidator
{
    /**
     * @var mixed
     */
    private $fromValue;
    private bool $equalFlag;

    /**
     * @param mixed $fromValue
     * @param string ...$fieldNames
     * @return MoreValidator
     */
    public static function fromBody($fromValue, string ...$fieldNames): MoreValidator
    {
        return new MoreValidator(
            $fromValue,
            false,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    /**
     * @param mixed $fromValue
     * @param string ...$headerNames
     * @return MoreValidator
     */
    public static function fromHeaders($fromValue, string ...$headerNames): MoreValidator
    {
        return new MoreValidator(
            $fromValue,
            false,
            new HeaderSelector(...$headerNames)
        );
    }

    /**
     * @param mixed $fromValue
     * @param string ...$attributeNames
     * @return MoreValidator
     */
    public static function fromAttributes($fromValue, string ...$attributeNames): MoreValidator
    {
        return new MoreValidator(
            $fromValue,
            false,
            new AttributeSelector(...$attributeNames)
        );
    }

    /**
     * @param mixed $fromValue
     * @param DataSelectorInterface ...$selectorList
     */
    public function __construct($fromValue, bool $equalFlag, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->fromValue = $fromValue;
        $this->equalFlag = $equalFlag;
    }

    public function withEqual(): MoreValidator
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

        if ($this->equalFlag && $this->fromValue > $item->value) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }

        if (!$this->equalFlag && $this->fromValue >= $item->value) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }
    }
}
