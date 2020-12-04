<?php


namespace BX\Router\PSR7;


use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Serializable;

class ResponseAdapterPSR implements ResponseInterface, Serializable
{
    const DEFAULT_HTTP_VERSION = '1.1';

    /**
     * @var HttpResponse
     */
    private $response;
    /**
     * @var string|null
     */
    private $httpVersion;
    /**
     * @var mixed|null
     */
    private $body;

    public function __construct(HttpResponse $response, string $httpVersion = null, $body = '')
    {
        $this->response = $response;
        $this->httpVersion = $httpVersion ?? static::DEFAULT_HTTP_VERSION;
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getProtocolVersion()
    {
        return $this->httpVersion;
    }

    /**
     * @param string $version
     * @return $this|ResponseAdapterPSR
     */
    public function withProtocolVersion($version)
    {
        return new static($this->response, $version, $this->body);
    }

    /**
     * @return array|string[][]
     */
    public function getHeaders()
    {
        return $this->response->getHeaders()->toArray();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return !empty($this->getHeader($name));
    }

    /**
     * @param string $name
     * @return array|string|string[]|null
     */
    public function getHeader($name)
    {
        return $this->response->getHeaders()->get($name, true);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        $value = $this->getHeader($name);
        if (empty($value)) {
            return '';
        }

        return implode(',', $value);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return $this|ResponseAdapterPSR
     */
    public function withHeader($name, $value)
    {
        $newResponse = clone $this->response;
        $newResponse->getHeaders()->set($name, $value);
        return new static($newResponse, $this->httpVersion, $this->body);
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return $this|ResponseAdapterPSR
     */
    public function withAddedHeader($name, $value)
    {
        if ($this->hasHeader($name)) {
            return $this;
        }

        return $this->withHeader($name, $value);
    }

    /**
     * @param string $name
     * @return $this|ResponseAdapterPSR
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $newResponse = clone $this->response;
        $newResponse->getHeaders()->delete($name);
        return new static($newResponse, $this->httpVersion, $this->body);
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        if (!$this->body) {
            $this->body = stream_for($this->response->getContent());
        }

        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return $this|ResponseAdapterPSR
     * @throws ArgumentTypeException
     */
    public function withBody(StreamInterface $body)
    {
        $newResponse = clone $this->response;
        $newResponse->setContent($body);

        return new static($newResponse, $this->httpVersion, $body);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        preg_match('/(\d+)\s+.*/', $this->response->getStatus(), $match);
        return (int)($match[1] ?? 200);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return $this|ResponseAdapterPSR
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $newResponse = clone $this->response;
        $newResponse->getHeaders()->set('Status', implode(' ', [$code, $reasonPhrase]));
        return new static($newResponse, $this->httpVersion, $this->body);
    }

    /**
     * @return mixed|string
     */
    public function getReasonPhrase()
    {
        preg_match('/\d+\s+(.*)/', $this->response->getStatus(), $match);
        return $match[1] ?? '';
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            'response' => $this->response,
            'http_version' => $this->httpVersion,
            'body' => (string)$this->body,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->response = $data['response'];
        $this->httpVersion = $data['http_version'];
        $this->body = $data['body'];
    }
}
