<?php

namespace BX\Router\Tests;
use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\DateInFutureValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use DateInterval;
use DateTimeImmutable;
use GuzzleHttp\Psr7\ServerRequest;

class DateInFutureValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $dateInFutureValidator = DateInFutureValidator::fromBody('Y-m-d', 'test_date');
        $now = new DateTimeImmutable();
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $bodyData = json_encode(['test_date' => $nextDay->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateInFutureValidator->validate($request);

        $bodyData = json_encode(['test_date' => $nextDay->format('d.m.Y')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $bodyData = json_encode(['test_date' => $prevDay->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $bodyData = json_encode(['test_date' => $now->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $dateInFutureValidator = DateInFutureValidator::fromBody('Y-m-d', 'test_date')
            ->withEqual();
        $dateInFutureValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateInFutureValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $dateInFutureValidator = DateInFutureValidator::fromHeaders('Y-m-d', 'Request-Date');
        $now = new DateTimeImmutable();
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $request = new ServerRequest('GET', '/test', ['Request-Date' => $nextDay->format('Y-m-d')]);
        $dateInFutureValidator->validate($request);

        $request = new ServerRequest('GET', '/test', ['Request-Date' => $nextDay->format('d.m.Y')]);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $request = new ServerRequest('GET', '/test', ['Request-Date' => $prevDay->format('Y-m-d')]);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $request = new ServerRequest('GET', '/test', ['Request-Date' => $now->format('Y-m-d')]);
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $dateInFutureValidator = DateInFutureValidator::fromHeaders('Y-m-d', 'Request-Date')
            ->withEqual();
        $dateInFutureValidator->validate($request);

        $request = new ServerRequest('GET', '/test', []);
        $dateInFutureValidator->validate($request);

    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $dateInFutureValidator = DateInFutureValidator::fromAttributes('Y-m-d', 'date');
        $now = new DateTimeImmutable();
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $nextDay->format('Y-m-d'));
        $dateInFutureValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $nextDay->format('d.m.Y'));
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $prevDay->format('Y-m-d'));
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $now->format('Y-m-d'));
        $this->testValidatorFailCase($dateInFutureValidator, $request, FormException::class);

        $dateInFutureValidator = DateInFutureValidator::fromAttributes('Y-m-d', 'date')
            ->withEqual();
        $dateInFutureValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'));
        $dateInFutureValidator->validate($request);
    }
}
