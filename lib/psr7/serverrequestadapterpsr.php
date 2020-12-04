<?php

namespace BX\Router\PSR7;

use Bitrix\Main\HttpRequest;
use BX\Router\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestAdapterPSR extends RequestAdapterPSR implements ServerRequestInterface
{
    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->request->getServer()->toArray();
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->request->getCookieList()->toArray();
    }

    /**
     * @param array $cookies
     * @return $this|RequestAdapterPSR
     */
    public function withCookieParams(array $cookies)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getCookieList()->setValues($cookies);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    public function getQueryParams()
    {
        return $this->request->getQueryList()->toArray();
    }

    public function withQueryParams(array $query)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getQueryList()->setValues($query);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return array_map(function (array $value) {
            return new UploadedFile(
                $value['tmp_name'],
                (int) $value['size'],
                (int) $value['error'],
                $value['name'],
                $value['type']
            );
        }, $this->request->getFileList()->toArray());
    }

    /**
     * @param array $uploadedFiles
     * @return $this|RequestAdapterPSR
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getFileList()->setValues($uploadedFiles);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->request->getPostList()->toArray();
    }

    /**
     * @param array|object|null $data
     * @return $this|RequestAdapterPSR
     */
    public function withParsedBody($data)
    {
        $newRequest = $this->getClonedRequest();
        $newRequest->getPostList()->setValues($data);

        return new static($newRequest, $this->httpVersion, $this->body, $this->attributes);
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $attribute
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($attribute, $default = null)
    {
        if (false === array_key_exists($attribute, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$attribute];
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return ServerRequestInterface
     */
    public function withAttribute($attribute, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;

        return $new;
    }

    /**
     * @param string $attribute
     * @return ServerRequestInterface
     */
    public function withoutAttribute($attribute): ServerRequestInterface
    {
        if (false === array_key_exists($attribute, $this->attributes)) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$attribute]);

        return $new;
    }
}
