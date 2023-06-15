<?php

namespace BX\Router\Middlewares\Validator;

use Psr\Http\Message\ServerRequestInterface;
use SplObjectStorage;

class RequestReader
{
    private SplObjectStorage $postData;

    public function __construct()
    {
        $this->postData = new SplObjectStorage();
    }

    public function getParsedPostData(ServerRequestInterface $request): array
    {
        /**
         * @psalm-suppress InvalidArgument
         */
        if (isset($this->postData[$request])) {
            return $this->postData[$request];
        }

        $data = json_decode($request->getBody()->getContents(), true);
        if ($data !== null) {
            /**
             * @psalm-suppress InvalidArgument
             */
            return $this->postData[$request] = $data;
        }

        /**
         * @psalm-suppress InvalidArgument,InvalidReturnStatement
         */
        return $this->postData[$request] = $request->getParsedBody() ?? [];
    }
}
