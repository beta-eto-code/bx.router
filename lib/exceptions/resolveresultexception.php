<?php


namespace BX\Router\Exceptions;


use Bitrix\Main\Result;
use BX\Router\Interfaces\AppFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResolveResultException extends HttpException
{
    public function __construct(
        Result $result,
        ServerRequestInterface $request = null,
        AppFactoryInterface $appFactory = null
    )
    {
        $error = current($result->getErrors());
        if (empty($error)) {
            parent::__construct(
                'Unknown error',
                ServerErrorException::CODE,
                ServerErrorException::PHRASE,
                $request,
                $appFactory
            );

            return;
        }

        $errorCode = (int)$error->getCode();
        switch ($errorCode) {
            case 400:
                $code = InvalidArgumentException::CODE;
                $phrase = InvalidArgumentException::PHRASE;
                break;
            case 403:
                $code = ForbiddenException::CODE;
                $phrase = ForbiddenException::PHRASE;
                break;
            case 404:
                $code = NotFoundException::CODE;
                $phrase = NotFoundException::PHRASE;
                break;
            default:
                $code = ServerErrorException::CODE;
                $phrase = ServerErrorException::PHRASE;
        }

        parent::__construct($error->getMessage(), $code, $phrase, $request, $appFactory);
    }
}
