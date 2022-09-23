<?php

namespace BX\Router\Middlewares;

use Bx\JWT\Interfaces\UserTokenServiceInterface;
use Bx\Model\Interfaces\AccessStrategyInterface;
use BX\Router\Exceptions\ForbiddenException;
use BX\Router\Interfaces\MiddlewareChainInterface;
use BX\Router\Middlewares\Traits\ChainHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;

class AuthJWT implements MiddlewareChainInterface
{
    use ChainHelper;

    /**
     * @var string
     */
    private $headerName;
    /**
     * @var UserTokenServiceInterface
     */
    private $tokenService;
    /**
     * @var AccessStrategyInterface|null
     */
    private $accessStrategy;

    /**
     * AuthJWT constructor.
     * @param string $headerName
     * @param UserTokenServiceInterface $tokenService
     * @param AccessStrategyInterface|null $accessStrategy
     */
    public function __construct(
        string $headerName,
        UserTokenServiceInterface $tokenService,
        AccessStrategyInterface $accessStrategy = null
    ) {
        $this->headerName = $headerName;
        $this->tokenService = $tokenService;
        $this->accessStrategy = $accessStrategy;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine($this->headerName);
        if (empty($header)) {
            return $this->runChain($request, $handler);
        }

        $token = str_replace('Bearer ', '', $header);
        try {
            $userContext = $this->tokenService->getUserContext($token);
            if ($this->accessStrategy instanceof AccessStrategyInterface) {
                $userContext->setAccessStrategy($this->accessStrategy);
            }
        } catch (UnexpectedValueException $e) {
            throw new ForbiddenException($e->getMessage());
        }
        return $this->runChain($request->withAttribute('user', $userContext), $handler);
    }
}
