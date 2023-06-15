<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\MoreValidator;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class MoreValidatorTest extends TestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $moreValidator = MoreValidator::fromBody( 5, 'number')->withEqual();
        $bodyData = json_encode(['number' => 7]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $moreValidator->validate($request);

        $bodyData = json_encode(['number' => 5]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $moreValidator->validate($request);

        $bodyData =  json_encode(['number' => 4]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $moreValidator = MoreValidator::fromBody(5, 'number');
        $bodyData =  json_encode(['number' => 5]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $moreValidator->validate($request);
    }

    public function testFromHeaders(): void
    {
        $moreValidator = MoreValidator::fromHeaders(10, 'UserId')
            ->withValueModifier(function ($value): int {
                return (int) $value;
            })
            ->withEqual();
        $request = new ServerRequest('GET', '/test', ['UserId' => '12']);
        $moreValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['UserId' => '10']);
        $moreValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['UserId' => '1']);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $moreValidator = MoreValidator::fromHeaders(10, 'UserId')
            ->withValueModifier(function ($value): int {
                return (int) $value;
            });

        $request = new ServerRequest('GET', '/test', ['UserId' => '10']);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $request = new ServerRequest('GET', '/test', []);
        $moreValidator->validate($request);

    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $moreValidator = MoreValidator::fromAttributes(1000, 'id')->withEqual();
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 4000);
        $moreValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1000);
        $moreValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 400);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $moreValidator = MoreValidator::fromAttributes(1000, 'id');
        $request = (new ServerRequest('POST', '/test'))->withAttribute('id', 1000);
        $this->expectException(FormException::class);
        $moreValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'));
        $moreValidator->validate($request);
    }
}
