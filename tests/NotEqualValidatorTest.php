<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\NotEqualValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use GuzzleHttp\Psr7\ServerRequest;

class NotEqualValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $notEqualValidator = NotEqualValidator::fromBody(['one', 'two', 'tree'], 'name');
        $bodyData =  json_encode(['name' => 'four']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $notEqualValidator->validate($request);

        $bodyData =  json_encode(['name' => 'tree']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $bodyData =  json_encode(['name' => 'one']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $bodyData =  json_encode(['name' => 'one']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $notEqualValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $notEqualValidator = NotEqualValidator::fromHeaders(
            [
                'multipart/form-data',
                'application/json'
            ],
            'Content-Type'
        );
        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/xml']);
        $notEqualValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'multipart/form-data']);
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $request = new ServerRequest('GET', '/test', ['Content-Type' => 'application/json']);
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $request = new ServerRequest('GET', '/test', []);
        $notEqualValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $notEqualValidator = NotEqualValidator::fromAttributes([10], 'id')->withStrictMode();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 20);
        $notEqualValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', '10');
        $notEqualValidator->validate($request);

        $notEqualValidator = NotEqualValidator::fromAttributes([10], 'id');
        $this->testValidatorFailCase($notEqualValidator, $request, FormException::class);

        $request = (new ServerRequest('POST', '/test'));
        $notEqualValidator->validate($request);
    }
}
