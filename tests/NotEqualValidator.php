<?php

namespace BX\Router\Tests;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class NotEqualValidator extends TestCase
{
    public function testFromBody(): void
    {
        $notEqualValidator = NotEqualValidator::fromBody('name', ['one', 'two', 'tree']);
        $bodyData =  json_encode(['name' => 'four']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $notEqualValidator->validate($request);

        $bodyData =  json_encode(['name' => 'tree']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $bodyData =  json_encode(['name' => 'one']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $bodyData =  json_encode(['name' => 'one']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $notEqualValidator->validate($request);
    }

    public function testFromHeaders(): void
    {
        $notEqualValidator = NotEqualValidator::fromHeaders(
            'Content-Type',
            [
                'multipart/form-data',
                'application/json'
            ]
        );
        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/xml']);
        $notEqualValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'multipart/form-data']);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/json']);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $request = new ServerRequest('GET', '/test', []);
        $notEqualValidator->validate($request);
    }

    public function testFromAttributes(): void
    {
        $notEqualValidator = NotEqualValidator::fromAttributes('id', [10])->withStrictMode();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 20);
        $notEqualValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', '10');
        $notEqualValidator->validate($request);

        $notEqualValidator = NotEqualValidator::fromBody('id', [10]);
        $this->expectException(FormException::class);
        $notEqualValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'));
        $notEqualValidator->validate($request);
    }
}
