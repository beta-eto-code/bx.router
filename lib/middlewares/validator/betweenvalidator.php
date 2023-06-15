<?php

namespace BX\Router\Middlewares\Validator;

use Exception;

class BetweenValidator extends BaseValidator
{
    /**
     * @var mixed
     */
    private $fromValue;
    /**
     * @var mixed
     */
    private $toValue;
    private bool $equalFlag;

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     * @param string ...$fieldNames
     * @return BetweenValidator
     */
    public static function fromBody($fromValue, $toValue, string ...$fieldNames): BetweenValidator
    {
        return new BetweenValidator(
            $fromValue,
            $toValue,
            false,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     * @param string ...$headerNames
     * @return BetweenValidator
     */
    public static function fromHeaders($fromValue, $toValue, string ...$headerNames): BetweenValidator
    {
        return new BetweenValidator(
            $fromValue,
            $toValue,
            false,
            new HeaderSelector(...$headerNames)
        );
    }

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     * @param string ...$attributeNames
     * @return BetweenValidator
     */
    public static function fromAttributes($fromValue, $toValue, string ...$attributeNames): BetweenValidator
    {
        return new BetweenValidator(
            $fromValue,
            $toValue,
            false,
            new AttributeSelector(...$attributeNames)
        );
    }

    /**
     * @param mixed $fromValue
     * @param mixed $toValue
     * @param bool $equalFlag
     * @param DataSelectorInterface ...$selectorList
     */
    public function __construct($fromValue, $toValue, bool $equalFlag, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->fromValue = $fromValue;
        $this->toValue = $toValue;
        $this->equalFlag = $equalFlag;
    }

    public function withEqual(): BetweenValidator
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

        if ($this->equalFlag && ($this->toValue < $item->value || $this->fromValue > $item->value)) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }

        if (!$this->equalFlag && ($this->toValue <= $item->value || $this->fromValue >= $item->value)) {
            throw new Exception($this->getDefaultErrorMessage($item));
        }
    }
}
