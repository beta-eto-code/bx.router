<?php

namespace BX\Router\Tests;
use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\MoreValidator;
use BX\Router\Middlewares\Validator\NotEqualValidator;
use BX\Router\Middlewares\Validator\RegexpValidator;
use BX\Router\Middlewares\Validator\RequiredValidator;
use BX\Router\Middlewares\Validator\ValueModifierFactory;
use BX\Router\Middlewares\ValidatorDataMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ValidatorDataMiddlewareTest extends TestCase
{
    /**
     * @throws FormException
     */
    public function testProcess(): void
    {
        $middleware = new ValidatorDataMiddleware(
            RequiredValidator::fromAttributes('id'),
            RequiredValidator::fromBody( 'name'),
            MoreValidator::fromAttributes(10, 'id')
                ->withValueModifier(ValueModifierFactory::toInt()),
            NotEqualValidator::fromBody(['drop', 'table'], 'name'),
            RegexpValidator::fromBody('/test_\d{2}/', 'code')
        );

        $mockedHandler = $this->createMock(RequestHandlerInterface::class);
        $body = json_encode([
            'name' => 'test',
            'code' => 'test_12'
        ]);
        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 123);
        $middleware->process($request, $mockedHandler);

        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 1);
        $this->testProcessMiddlewareFailCase($middleware, $request, $mockedHandler, FormException::class);

        $body = json_encode([
            'name' => 'test',
            'code' => 'test_1'
        ]);
        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 123);
        $this->testProcessMiddlewareFailCase($middleware, $request, $mockedHandler, FormException::class);

        $body = json_encode([
            'code' => 'test_12'
        ]);
        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 123);
        $this->testProcessMiddlewareFailCase($middleware, $request, $mockedHandler, FormException::class);

        $body = json_encode([
            'name' => 'drop',
            'code' => 'test_12'
        ]);
        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 123);
        $this->testProcessMiddlewareFailCase($middleware, $request, $mockedHandler, FormException::class);

        $body = json_encode([
            'name' => 'table',
            'code' => 'test_12'
        ]);
        $request = (new ServerRequest('POST', '/test/123', [], $body))
            ->withAttribute('id', 123);
        $this->testProcessMiddlewareFailCase($middleware, $request, $mockedHandler, FormException::class);
    }

    private function testProcessMiddlewareFailCase(
        MiddlewareInterface $middleware,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        string $exceptionClass
    ): void {
        $hasException = false;
        try {
            $middleware->process($request, $handler);
        } catch (Throwable $exception) {
            $hasException = true;
            $this->assertEquals($exceptionClass, get_class($exception));
        }

        if (!$hasException) {
            $this->fail("Expected exception by class $exceptionClass");
        }
    }
}
