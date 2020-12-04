<?php


namespace BX\Router\PSR7;


use Bitrix\Main\HttpRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestAdapterPSR extends Message implements RequestInterface
{
    /**
     * @return string
     */
    public function getRequestTarget()
    {
        return (string)$this->request->getRequestUri();
    }

    /**
     * @param mixed $requestTarget
     * @return $this|RequestAdapterPSR
     */
    public function withRequestTarget($requestTarget)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getServer()->set('REQUEST_URI', $requestTarget);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        return $this->request->getRequestMethod();
    }

    /**
     * @param string $method
     * @return $this|RequestAdapterPSR
     */
    public function withMethod($method)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getServer()->set('REQUEST_METHOD', $method);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param false $preserveHost
     * @return $this|RequestAdapterPSR
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getServer()->set('REQUEST_URI', $uri);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return HttpRequest
     */
    protected function getClonedRequest(): HttpRequest
    {
        return clone $this->request;
    }
}
