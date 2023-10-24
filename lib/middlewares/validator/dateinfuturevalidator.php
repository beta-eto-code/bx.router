<?php

namespace BX\Router\Middlewares\Validator;

use DateTimeImmutable;
use Exception;

class DateInFutureValidator extends BaseValidator
{
    private string $dateFormat;

    public static function fromBody(string $dateFormat, string ...$fieldNames): DateInFuture
    {
        return new DateInFuture($dateFormat, new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader()));
    }

    public function __construct(string $dateFormat, DataSelectorInterface ...$selectorList)
    {
        parent::__construct(...$selectorList);
        $this->dateFormat = $dateFormat;
    }

    /**
     * @throws Exception
     */
    protected function validateItem(SelectorItem $item): void
    {
        $date = DateTimeImmutable::createFromFormat($this->dateFormat, $item->value);
        if (empty($date)) {
            throw new Exception($item->getSubjectItemName() . ' ' . $item->key . ': не верный формат даты');
        }

        if ($date->getTimestamp() <= time()) {
            throw new Exception($item->getSubjectItemName() . ' ' . $item->key . ': не верная дата');
        }
    }
}
