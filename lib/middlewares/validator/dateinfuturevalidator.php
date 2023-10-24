<?php

namespace BX\Router\Middlewares\Validator;

use DateTimeImmutable;
use Exception;

class DateInFutureValidator extends BaseValidator
{
    private DateValidator $dateValidator;

    public static function fromBody(string $dateFormat, string ...$fieldNames): DateInFutureValidator
    {
        return new DateInFutureValidator(
            $dateFormat,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    public static function fromHeaders(string $dateFormat, string ...$headerNames): DateInFutureValidator
    {
        return new DateInFutureValidator(
            $dateFormat,
            new HeaderSelector(...$headerNames)
        );
    }

    public static function fromAttributes(string $dateFormat, string ...$attributeNames): DateInFutureValidator
    {
        return new DateInFutureValidator(
            $dateFormat,
            new AttributeSelector(...$attributeNames)
        );
    }

    public function __construct(string $dateFormat, DataSelectorInterface ...$selectorList)
    {
        $this->dateValidator = (new DateValidator($dateFormat))->withLimitFromDate(new DateTimeImmutable());
        parent::__construct(...$selectorList);
    }

    public function withEqual(): DateInFutureValidator
    {
        $newValidator = clone $this;
        $newValidator->dateValidator = $newValidator->dateValidator->withEqual();
        return $newValidator;
    }

    /**
     * @throws Exception
     */
    protected function validateItem(SelectorItem $item): void
    {
        $this->dateValidator->validateItem($item);
    }
}
