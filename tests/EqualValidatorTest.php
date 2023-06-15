<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\EqualValidator;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class EqualValidatorTest extends TestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $equalValidator = EqualValidator::fromBody(['one', 'two', 'tree'], 'name');
        $bodyData =  json_encode(['name' => 'one']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $equalValidator->validate($request);

        $bodyData =  json_encode(['name' => 'two']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $equalValidator->validate($request);

        $bodyData =  json_encode(['name' => 'tree']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $equalValidator->validate($request);

        $bodyData =  json_encode(['name' => 'other']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $equalValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $equalValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $equalValidator = EqualValidator::fromHeaders(
            [
                'multipart/form-data',
                'application/json'
            ],
            'Content-Type'
        );
        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/json']);
        $equalValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'multipart/form-data']);
        $equalValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/xml']);
        $this->expectException(FormException::class);
        $equalValidator->validate($request);

        $request = new ServerRequest('GET', '/test', []);
        $equalValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $equalValidator = EqualValidator::fromAttributes([10], 'id')->withStrictMode();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 10);
        $equalValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', '10');
        $this->expectException(FormException::class);
        $equalValidator->validate($request);

        $equalValidator = EqualValidator::fromBody([10], 'id');
        $equalValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 20);
        $this->expectException(FormException::class);
        $equalValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'));
        $equalValidator->validate($request);
    }
}
