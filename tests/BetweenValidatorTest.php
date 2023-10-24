<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\BetweenValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use GuzzleHttp\Psr7\ServerRequest;

class BetweenValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $compareValidator = BetweenValidator::fromBody(5, 10, 'number');
        $bodyData = json_encode(['number' => 7]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $compareValidator->validate($request);

        $bodyData =  json_encode(['number' => 1]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
         $this->testValidatorFailCase($compareValidator, $request, FormException::class);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $compareValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $compareValidator = BetweenValidator::fromHeaders(10, 40, 'UserId')
            ->withValueModifier(function ($value): int {
                return (int) $value;
            });
        $request = new ServerRequest('GET', '/test', ['UserId' => '32']);
        $compareValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['UserId' => '144']);
         $this->testValidatorFailCase($compareValidator, $request, FormException::class);

        $request = new ServerRequest('GET', '/test', []);
        $compareValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $compareValidator = BetweenValidator::fromAttributes(1000, 1455, 'id')
            ->withEqual();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1455);
        $compareValidator->validate($request);

        $compareValidator = BetweenValidator::fromAttributes(1000, 1455, 'id');
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1455);
         $this->testValidatorFailCase($compareValidator, $request, FormException::class);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1456);
         $this->testValidatorFailCase($compareValidator, $request, FormException::class);

        $request = (new ServerRequest('POST', '/test'));
        $compareValidator->validate($request);
    }
}
