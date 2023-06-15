<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\RegexpValidator;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RegexpValidatorTest extends TestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $regexpValidator = RegexpValidator::fromBody('/test_\d+/', 'field_one', 'field_two');
        $bodyData = json_encode(['field_one' => 'test_123', 'field_two' => 'test_1']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $regexpValidator->validate($request);

        $bodyData = json_encode(['field_one' => 'test_qweqwe']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $bodyData = json_encode(['field_two' => 'test_sdfsdf']);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $bodyData = json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $regexpValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $regexpValidator = RegexpValidator::fromHeaders('/Bearer\s\S+/', 'Authorization');
        $request = new ServerRequest('GET', '/test', ['Authorization' => 'Bearer test1234']);
        $regexpValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Authorization' => 'Bearer_test1234']);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Authorization' => '144']);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Authorization' => 144]);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $request = new ServerRequest('GET', '/test', []);
        $regexpValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $regexpValidator = RegexpValidator::fromAttributes('/id_\d+/', 'internalId');
        $request = (new ServerRequest('POST', '/test'))->withAttribute('internalId', 'id_400');
        $regexpValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('internalId', 400);
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('internalId', 'id');
        $this->expectException(FormException::class);
        $regexpValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'));
        $regexpValidator->validate($request);
    }
}
