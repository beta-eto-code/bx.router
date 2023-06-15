<?php

namespace BX\Router\Tests;
use ArrayIterator;
use BX\Router\Middlewares\Validator\AttributeSelector;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AttributeSelectorTest extends TestCase
{
    public function testGetDataIterable()
    {
        $attributeSelector = new AttributeSelector('id', 'sectionId', 'test');
        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute('id', 1234)
            ->withAttribute('sectionId', 1);

        $result = $attributeSelector->getDataIterable($request);
        if (is_array($result)) {
            $result = new ArrayIterator($result);
        }

        $this->assertEquals('id', $result->key());
        $this->assertEquals(1234, $result->current());

        $result->next();
        $this->assertEquals('sectionId', $result->key());
        $this->assertEquals(1, $result->current());

        $result->next();
        $this->assertEquals('test', $result->key());
        $this->assertEquals(null, $result->current());
    }
}
