<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\BodyDataSelector;
use BX\Router\Middlewares\Validator\RequestReader;
use BX\Router\Middlewares\Validator\RequiredValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use GuzzleHttp\Psr7\ServerRequest;

class RequiredValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testValidate(): void
    {
        $requestReader = new RequestReader();
        $bodySelector = new BodyDataSelector(['name', 'email'], $requestReader);
        $requiredValidator = new RequiredValidator($bodySelector);

        $dataRequest = ['email' => 'test@mail.ru', 'name' => 'alex'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $requiredValidator->validate($request);

        $dataRequest = ['email' => 'test@mail.ru'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);

        $dataRequest = ['name' => 'alex'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);
    }

    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $requiredValidator = RequiredValidator::fromBody('name', 'email');
        $dataRequest = ['email' => 'test@mail.ru', 'name' => 'alex'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $requiredValidator->validate($request);

        $dataRequest = ['email' => 'test@mail.ru'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);

        $dataRequest = ['name' => 'alex'];
        $request = new ServerRequest('GET', '/test', [], json_encode($dataRequest), '1.1');
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $requiredValidator = RequiredValidator::fromHeaders('Authorization', 'Content-Type');
        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Authorization' => 'Bearer some_token',
                'Content-Type' => 'application/json'
            ]
        );
        $requiredValidator->validate($request);

        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Authorization' => 'Bearer some_token',
            ]
        );
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);

        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Content-Type' => 'application/json'
            ]
        );
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $requiredValidator = RequiredValidator::fromAttributes('id', 'sectionId');
        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute('id', 1234)
            ->withAttribute('sectionId', 1);
        $requiredValidator->validate($request);

        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute('id', 1234);
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);

        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute('sectionId', 1);
        $this->testValidatorFailCase($requiredValidator, $request, FormException::class);
    }
}
