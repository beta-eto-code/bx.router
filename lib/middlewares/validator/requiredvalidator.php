<?php

namespace BX\Router\Middlewares\Validator;

use BX\Router\Exceptions\FormException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class RequiredValidator extends BaseValidator
{
    public static function fromBody(string ...$fieldNames): RequiredValidator
    {
        return new RequiredValidator(new BodyDataSelector($fieldNames, Factory::getOrCreateRequestReader()));
    }

    public static function fromHeaders(string ...$headerNames): RequiredValidator
    {
        return new RequiredValidator(new HeaderSelector(...$headerNames));
    }

    public static function fromAttribute(string ...$attributeNames): RequiredValidator
    {
        return new RequiredValidator(new AttributeSelector(...$attributeNames));
    }

    /**
     * @throws Exception
     */
    protected function validateItem(SelectorItem $item): void
    {
        if (empty($item->value)) {
            throw new Exception($item->getSubjectItemName() . " $item->key не может пустым");
        }
    }
}
