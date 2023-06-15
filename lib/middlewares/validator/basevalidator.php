<?php

namespace BX\Router\Middlewares\Validator;

use BX\Router\Exceptions\FormException;
use Closure;
use EmptyIterator;
use Psr\Http\Message\ServerRequestInterface;
use Iterator;
use Throwable;

abstract class BaseValidator implements ValidatorDataInterface
{
    /**
     * @var DataSelectorInterface[]
     */
    private array $selectorList;

    /**
     * @var callable|null
     */
    protected $valueModifier = null;

    public function __construct(DataSelectorInterface ...$selectorList)
    {
        $this->selectorList = $selectorList;
    }

    abstract protected function validateItem(SelectorItem $item): void;

    public function withSelectors(DataSelectorInterface ...$selectorList): BaseValidator
    {
        $newValidator = clone $this;
        $newValidator->selectorList = array_merge($newValidator->selectorList, $selectorList);
        return $newValidator;
    }

    public function withValueModifier(callable $valueModifier): BaseValidator
    {
        $newValidator = clone $this;
        $newValidator->valueModifier = $valueModifier;
        return $newValidator;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Iterator|SelectorItem[]
     * @psalm-suppress MismatchingDocblockReturnType
     */
    protected function getSelectorValuesIterator(ServerRequestInterface $request): Iterator
    {
        foreach ($this->selectorList as $selector) {
            foreach ($selector->getDataIterable($request) as $key => $value) {
                if (is_callable($this->valueModifier)) {
                    $value = ($this->valueModifier)($value);
                }
                yield new SelectorItem((string)$key, $value, $selector);
            }
        }
        return new EmptyIterator();
    }

    public function validate(ServerRequestInterface $request): void
    {
        $formException = new FormException();
        foreach ($this->getSelectorValuesIterator($request) as $selectorItem) {
            try {
                $this->validateItem($selectorItem);
            } catch (Throwable $exception) {
                $formException->addErrorField($selectorItem->key, $exception->getMessage());
            }
        }

        if ($formException->hasErrors()) {
            throw $formException;
        }
    }

    protected function getDefaultErrorMessage(SelectorItem $item): string
    {
        return $item->getSubjectItemName() . ' ' . $item->key . ': недопустимое значение';
    }
}
