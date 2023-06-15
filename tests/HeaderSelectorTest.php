<?php

namespace BX\Router\Tests;

use ArrayIterator;
use BX\Router\Middlewares\Validator\HeaderSelector;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class HeaderSelectorTest extends TestCase
{
    public function testGetDataIterable()
    {
        $headerSelector = new HeaderSelector('Authorization', 'Content-Type', 'Test');
        $request = new ServerRequest(
            'GET',
            '/test',
            [
                'Authorization' => 'Bearer some_token',
                'Content-Type' => 'application/json'
            ]
        );

        $result = $headerSelector->getDataIterable($request);
        if (is_array($result)) {
            $result = new ArrayIterator($result);
        }

        $this->assertEquals('Authorization', $result->key());
        $this->assertEquals('Bearer some_token', $result->current());

        $result->next();
        $this->assertEquals('Content-Type', $result->key());
        $this->assertEquals('application/json', $result->current());

        $result->next();
        $this->assertEquals('Test', $result->key());
        $this->assertNull($result->current());
    }
}
