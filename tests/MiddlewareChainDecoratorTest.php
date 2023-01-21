<?php

namespace BX\Router\Tests;

use ArrayIterator;
use BX\Router\MiddlewareChainDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareChainDecoratorTest extends TestCase
{
    public function testGetMiddleware()
    {
        $originalMiddleware = $this->createMiddlewareAddAttribute("one", 1);
        $middlewareChain = new MiddlewareChainDecorator($originalMiddleware);
        $this->assertNull($middlewareChain->getMiddleware());
    }

    public function testAddMiddleware()
    {
        $originalMiddleware = $this->createMiddlewareAddAttribute("one", 1);
        $middlewareChain = new MiddlewareChainDecorator($originalMiddleware);
        $this->assertNull($middlewareChain->getMiddleware());

        $newMiddleware = $this->createMiddlewareAddAttribute("two", 2);
        $newMiddlewareChain = $middlewareChain->addMiddleware($newMiddleware);
        $this->assertNotNull($middlewareChain->getMiddleware());

        $this->assertNull($newMiddlewareChain->getMiddleware());
        $oneMoreMiddleware = $this->createMiddlewareAddAttribute("tree", 3);
        $newMiddlewareChain->addMiddleware($oneMoreMiddleware);
        $this->assertNotNull($newMiddlewareChain->getMiddleware());
    }

    public function testProcess()
    {
        $originalMiddleware = $this->createMiddlewareAddAttribute("one", 1);
        $middlewareChain = new MiddlewareChainDecorator($originalMiddleware);

        $attributeList = [];
        $request = $this->createServerRequest($attributeList);
        $middlewareChain->process($request, $this->createHandler());
        $this->assertCount(1, $attributeList);
        $this->assertArrayHasKey("one", $attributeList);
        $this->assertEquals(1, $attributeList["one"]);

        $attributeList = [];
        $request = $this->createServerRequest($attributeList);
        $newMiddleware = $this->createMiddlewareAddAttribute("two", 2);
        $newMiddlewareChain = $middlewareChain->addMiddleware($newMiddleware);
        $oneMoreMiddleware = $this->createMiddlewareAddAttribute("tree", 3);
        $newMiddlewareChain->addMiddleware($oneMoreMiddleware);

        $middlewareChain->process($request, $this->createHandler());
        $this->assertCount(3, $attributeList);
        $this->assertArrayHasKey("one", $attributeList);
        $this->assertArrayHasKey("two", $attributeList);
        $this->assertArrayHasKey("tree", $attributeList);
        $this->assertEquals(1, $attributeList["one"]);
        $this->assertEquals(2, $attributeList["two"]);
        $this->assertEquals(3, $attributeList["tree"]);

        $attributeIterator = new ArrayIterator($attributeList);
        $this->assertEquals("one", $attributeIterator->key());
        $this->assertEquals(1, $attributeIterator->current());
        $attributeIterator->next();
        $this->assertEquals("two", $attributeIterator->key());
        $this->assertEquals(2, $attributeIterator->current());
        $attributeIterator->next();
        $this->assertEquals("tree", $attributeIterator->key());
        $this->assertEquals(3, $attributeIterator->current());
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return MiddlewareInterface
     */
    private function createMiddlewareAddAttribute(string $name, $value)
    {
        $middlewareStub = $this->createMock(MiddlewareInterface::class);
        $middlewareStub->method('process')
            ->willReturnCallback($this->createMiddlewareCallbackAddAttribute($name, $value));
        return $middlewareStub;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return callable
     */
    private function createMiddlewareCallbackAddAttribute(string $name, $value): callable
    {
        return function (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ) use ($name, $value): ResponseInterface {
            $request = $request->withAttribute($name, $value);
            return $handler->handle($request);
        };
    }

    /**
     * @param array $attributes
     * @return ServerRequestInterface
     */
    private function createServerRequest(array &$attributes)
    {
        $requestStub = $this->createMock(ServerRequestInterface::class);
        $requestStub->method('withAttribute')->willReturnCallback(
            function (string $name, $value) use (&$attributes, $requestStub) {
                $attributes[$name] = $value;
                return $requestStub;
        });

        return $requestStub;
    }

    /**
     * @return RequestHandlerInterface
     */
    private function createHandler()
    {
        return $this->createMock(RequestHandlerInterface::class);
    }
}
