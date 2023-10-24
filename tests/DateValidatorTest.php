<?php

namespace BX\Router\Tests;

use BX\Router\Exceptions\FormException;
use BX\Router\Middlewares\Validator\DateValidator;
use BX\Router\Tests\Utils\ValidatorTestCase;
use DateInterval;
use DateTimeImmutable;
use GuzzleHttp\Psr7\ServerRequest;

class DateValidatorTest extends ValidatorTestCase
{
    /**
     * @throws FormException
     */
    public function testFromBody(): void
    {
        $dateValidator = DateValidator::fromBody('Y-m-d', 'test_date');
        $now = new DateTimeImmutable();
        $bodyData = json_encode(['test_date' => $now->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);

        $bodyData = json_encode(['test_date' => $now->format('d.m.Y')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateLimitFrom = $now->sub(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromBody('Y-m-d', 'test_date')
            ->withLimitFromDate($dateLimitFrom);
        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $bodyData = json_encode(['test_date' => $prevDay->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);

        $bodyData = json_encode(['test_date' => $dateLimitFrom->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromBody('Y-m-d', 'test_date')
            ->withLimitFromDate($dateLimitFrom)
            ->withEqual();
        $dateValidator->validate($request);

        $dateLimitTo = $now->add(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromBody('Y-m-d', 'test_date')
            ->withLimitToDate($dateLimitTo);
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $bodyData = json_encode(['test_date' => $nextDay->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);

        $bodyData = json_encode(['test_date' => $dateLimitTo->format('Y-m-d')]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromBody('Y-m-d', 'test_date')
            ->withLimitToDate($dateLimitTo)
            ->withEqual();
        $dateValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromHeaders(): void
    {
        $dateValidator = DateValidator::fromHeaders('Y-m-d', 'Request-Date');
        $now = new DateTimeImmutable();
        $request = new ServerRequest('POST', '/test', ['Request-Date' => $now->format('Y-m-d')]);
        $dateValidator->validate($request);

        $request = new ServerRequest('POST', '/test', ['Request-Date' => $now->format('d.m.Y')]);
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateLimitFrom = $now->sub(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromHeaders('Y-m-d', 'Request-Date')
            ->withLimitFromDate($dateLimitFrom);
        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $request = new ServerRequest('POST', '/test', ['Request-Date' => $prevDay->format('Y-m-d')]);
        $dateValidator->validate($request);

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Request-Date' => $dateLimitFrom->format('Y-m-d')]
        );
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromHeaders('Y-m-d', 'Request-Date')
            ->withLimitFromDate($dateLimitFrom)
            ->withEqual();
        $dateValidator->validate($request);

        $dateLimitTo = $now->add(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromHeaders('Y-m-d', 'Request-Date')
            ->withLimitToDate($dateLimitTo);
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $request = new ServerRequest(
            'POST',
            '/test',
            ['Request-Date' => $nextDay->format('Y-m-d')]
        );
        $dateValidator->validate($request);

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Request-Date' => $dateLimitTo->format('Y-m-d')]
        );
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromHeaders('Y-m-d', 'Request-Date')
            ->withLimitToDate($dateLimitTo)
            ->withEqual();
        $dateValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);
    }

    /**
     * @throws FormException
     */
    public function testFromAttributes(): void
    {
        $dateValidator = DateValidator::fromAttributes('Y-m-d', 'date');
        $now = new DateTimeImmutable();
        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $now->format('Y-m-d'));
        $dateValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $now->format('d.m.Y'));
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateLimitFrom = $now->sub(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromAttributes('Y-m-d', 'date')
            ->withLimitFromDate($dateLimitFrom);
        $prevDay = $now->sub(DateInterval::createFromDateString('1 day'));
        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $prevDay->format('Y-m-d'));
        $dateValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $dateLimitFrom->format('Y-m-d'));
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromAttributes('Y-m-d', 'date')
            ->withLimitFromDate($dateLimitFrom)
            ->withEqual();
        $dateValidator->validate($request);

        $dateLimitTo = $now->add(DateInterval::createFromDateString('10 days'));
        $dateValidator = DateValidator::fromAttributes('Y-m-d', 'date')
            ->withLimitToDate($dateLimitTo);
        $nextDay = $now->add(DateInterval::createFromDateString('1 day'));
        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $nextDay->format('Y-m-d'));
        $dateValidator->validate($request);

        $request = (new ServerRequest('POST', '/test'))
            ->withAttribute('date', $dateLimitTo->format('Y-m-d'));
        $this->testValidatorFailCase($dateValidator, $request, FormException::class);

        $dateValidator = DateValidator::fromAttributes('Y-m-d', 'date')
            ->withLimitToDate($dateLimitTo)
            ->withEqual();
        $dateValidator->validate($request);

        $bodyData =  json_encode([]);
        $request = new ServerRequest('POST', '/test', [], $bodyData);
        $dateValidator->validate($request);
    }
}
