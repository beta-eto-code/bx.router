<?php

namespace BX\Router\Middlewares\Validator;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class DateValidator extends BaseValidator
{
    private string $dateFormat;
    private ?DateTimeInterface $fromDate = null;
    private ?DateTimeInterface $toDate = null;
    private bool $equalFlag = false;

    public static function fromBody(string $dateFormat, string ...$fieldNames): DateValidator
    {
        return new DateValidator(
            $dateFormat,
            new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader())
        );
    }

    public static function fromHeaders(string $dateFormat, string ...$headerNames): DateValidator
    {
        return new DateValidator(
            $dateFormat,
            new HeaderSelector(...$headerNames)
        );
    }

    public static function fromAttributes(string $dateFormat, string ...$attributeNames): DateValidator
    {
        return new DateValidator(
            $dateFormat,
            new AttributeSelector(...$attributeNames)
        );
    }

    /**
     * @param string $dateFormat
     * @param DataSelectorInterface ...$selectorList
     */
    public function __construct(
        string $dateFormat,
        DataSelectorInterface ...$selectorList
    ) {
        parent::__construct(...$selectorList);
        $this->dateFormat = $dateFormat;
    }

    public function withLimitFromDate(DateTimeInterface $fromDate): DateValidator
    {
        $newValidator = clone $this;
        $newValidator->fromDate = $fromDate;
        return $newValidator;
    }

    public function withLimitToDate(DateTimeInterface $toDate): DateValidator
    {
        $newValidator = clone $this;
        $newValidator->toDate = $toDate;
        return $newValidator;
    }

    public function withEqual(): DateValidator
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

        $date = DateTimeImmutable::createFromFormat($this->dateFormat, $item->value);
        if (empty($date)) {
            throw new Exception($item->getSubjectItemName() . ' ' . $item->key . ': не верный формат даты');
        }

        $errorMessage = $item->getSubjectItemName() . ' ' . $item->key . ': не верная дата';
        $this->validateFromDateRule($date, $errorMessage);
        $this->validateToDateRule($date, $errorMessage);
    }

    /**
     * @throws Exception
     */
    private function validateFromDateRule(DateTimeInterface $date, string $errorMessage): void
    {
        if (empty($this->fromDate)) {
            return;
        }

        if (!$this->equalFlag && $date->getTimestamp() <= $this->fromDate->getTimestamp()) {
            throw new Exception($errorMessage);
        } elseif ($this->equalFlag && $date->getTimestamp() < $this->fromDate->getTimestamp()) {
            throw new Exception($errorMessage);
        }
    }

    /**
     * @throws Exception
     */
    private function validateToDateRule(DateTimeInterface $date, string $errorMessage): void
    {
        if (empty($this->toDate)) {
            return;
        }

        if (!$this->equalFlag && $date->getTimestamp() >= $this->toDate->getTimestamp()) {
            throw new Exception($errorMessage);
        } elseif ($this->equalFlag && $date->getTimestamp() > $this->toDate->getTimestamp()) {
            throw new Exception($errorMessage);
        }
    }
}
