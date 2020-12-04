<?php


namespace BX\Router;


use BX\Router\Interfaces\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler implements ResponseHandlerInterface
{
    private function setHeaders(array $headers)
    {
        foreach ($headers as $item) {
            $key = $item['name'];
            $value = $item['values'];
            $value = trim(is_array($value) ? implode(',', $value) : $value);
            header("{$key}: {$value}");
        }
    }

    public function handle(ResponseInterface $response)
    {
        $this->setHeaders($response->getHeaders());
        $status = implode(' ', [
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ]);
        //header("HTTP/{$response->getProtocolVersion()} {$status}");
        echo $response->getBody();
    }
}
