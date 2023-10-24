<?php

namespace BX\Router\Tests\Utils;

use BX\Router\Middlewares\Validator\ValidatorDataInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class ValidatorTestCase extends TestCase
{
    protected function testValidatorFailCase(
        ValidatorDataInterface $validator,
        ServerRequestInterface $request,
        string $exceptionClass
    ): void {
        $hasException = false;
        try {
            $validator->validate($request);
        } catch (Throwable $exception) {
            $hasException = true;
            $this->assertEquals($exceptionClass, get_class($exception));
        }

        if (!$hasException) {
            $this->fail("Expected exception by class $exceptionClass");
        }
    }
}
