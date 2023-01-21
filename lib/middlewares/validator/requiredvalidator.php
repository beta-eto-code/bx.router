<?php

namespace BX\Router\Middlewares;

use BX\Router\Exceptions\FormException;
use Psr\Http\Message\ServerRequestInterface;

class RequiredValidator implements ValidatorDataInterface
{
    /**
     * @var DataSelectorInterface[]
     */
    private array $selectorList;

    public function __construct(DataSelectorInterface ...$selectorList)
    {
        $this->selectorList = $selectorList;
    }

    public function validate(ServerRequestInterface $request): void
    {
        $formException = new FormException();
        foreach ($this->selectorList as $selector) {
            foreach ($selector->getDataIterable($request) as $key => $value) {
                if (empty($value)) {
                    $formException->addErrorField($key, 'не может пустым');
                }
            }
        }

        if ($formException->hasErrors()) {
            throw $formException;
        }
    }
}
