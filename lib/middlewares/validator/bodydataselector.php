<?php

namespace BX\Router\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use SplObjectStorage;

class BodyDataSelector implements DataSelectorInterface
{
    private static ?SplObjectStorage $postData = null;
    /**
     * @var string[]
     */
    private array $fieldNames;

    public function __construct(string ...$fieldNames)
    {
        $this->fieldNames = $fieldNames;
    }

    public function getDataIterable(ServerRequestInterface $request): iterable
    {
        if (empty($this->fieldNames)) {
            return [];
        }

        $result = [];
        $data = $this->getParsedPostData($request);
        foreach ($this->fieldNames as $fieldName) {
            $result[$fieldName] = $data[$fieldName] ?? null;
        }
        return $result;
    }

    protected function getParsedPostData(ServerRequestInterface $request): array
    {
        if (is_null(static::$postData)) {
            static::$postData = new SplObjectStorage();
        }

        /**
         * @psalm-suppress InvalidArgument
         */
        if (isset(static::$postData[$request])) {
            return static::$postData[$request];
        }

        $data = json_decode($request->getBody()->getContents(), true);
        if ($data !== null) {
            /**
             * @psalm-suppress InvalidArgument
             */
            return static::$postData[$request] = $data;
        }

        /**
         * @psalm-suppress InvalidArgument,InvalidReturnStatement
         */
        return static::$postData[$request] = $request->getParsedBody() ?? [];
    }
}
