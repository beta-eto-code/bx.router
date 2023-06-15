<?php

namespace BX\Router\Middlewares\Validator;

use Psr\Http\Message\ServerRequestInterface;
use SplObjectStorage;

class BodyDataSelector implements DataSelectorInterface
{
    /**
     * @var string[]
     */
    private array $fieldNames;
    private RequestReader $requestReader;

    public function __construct(array $fieldNames, ?RequestReader $requestReader = null)
    {
        $this->fieldNames = $fieldNames;
        $this->requestReader = $requestReader ?? new RequestReader();
    }

    public function getDataIterable(ServerRequestInterface $request): iterable
    {
        if (empty($this->fieldNames)) {
            return [];
        }

        $result = [];
        $data = $this->requestReader->getParsedPostData($request);
        foreach ($this->fieldNames as $fieldName) {
            $result[$fieldName] = $data[$fieldName] ?? null;
        }
        return $result;
    }

    public static function getSubjectItemName(): string
    {
        return 'поле';
    }
}
