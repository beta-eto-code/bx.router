<?php


namespace BX\Router;


use BX\Router\Interfaces\ResponseHandlerInterface;
use CHTTP;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler implements ResponseHandlerInterface
{
    private function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $value = trim(is_array($value) ? implode(',', $value) : $value);

            if (strtolower($key) === 'status') {
                CHTTP::SetStatus($value);
            } else {
                header("{$key}: {$value}");
            }
        }
    }

    public function handle(ResponseInterface $response)
    {
        $this->setHeaders($response->getHeaders());
        echo $response->getBody();
    }
}
