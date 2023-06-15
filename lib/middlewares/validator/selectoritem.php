<?php

namespace BX\Router\Middlewares\Validator;

class SelectorItem
{
    public string $key;
    /**
     * @var mixed
     */
    public $value;
    private DataSelectorInterface $selector;

    /**
     * @param string $key
     * @param mixed $value
     * @param DataSelectorInterface $selector
     */
    public function __construct(string $key, $value, DataSelectorInterface $selector)
    {
        $this->key = $key;
        $this->value = $value;
        $this->selector = $selector;
    }

    public function getSubjectItemName(): string
    {
        return $this->selector::getSubjectItemName();
    }
}
