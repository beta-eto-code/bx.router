<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\LessValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use GuzzleHttp\Psr7\ServerRequest;

class LessValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $lessValidator = LessValidator::fromBody(5, 'number')->withEqual();
        $bodyData = json_encode(['number' => 4]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $lessValidator->validate($request);

        $bodyData = json_encode(['number' => 5]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $lessValidator->validate($request);

        $bodyData =  json_encode(['number' => 7]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $lessValidator = LessValidator::fromBody(5, 'number');
        $bodyData =  json_encode(['number' => 5]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $lessValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $lessValidator = LessValidator::fromHeaders(10, 'UserId')
            ->withEqual()
            ->withValueModifier(function ($value): int {
                return (int) $value;
            });
        $request = new ServerRequest('GET', '/test', ['UserId' => '8']);
        $lessValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['UserId' => '10']);
        $lessValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['UserId' => '144']);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $lessValidator = LessValidator::fromHeaders(10, 'UserId')
            ->withValueModifier(function ($value): int {
                return (int) $value;
            });

        $request = new ServerRequest('GET', '/test', ['UserId' => '10']);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $request = new ServerRequest('GET', '/test');
        $lessValidator->validate($request);

    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $lessValidator = LessValidator::fromAttributes(1000, 'id')->withEqual();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 400);
        $lessValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1000);
        $lessValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1456);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $lessValidator = LessValidator::fromAttributes(1000, 'id');
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1000);
        $this->testValidatorFailCase($lessValidator, $request, FormException::class);

        $request = (new ServerRequest('POST', '/test'));
        $lessValidator->validate($request);
    }
}
