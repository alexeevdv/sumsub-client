<?php

namespace alexeevdv\SumSub\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class DocumentImagesResponse
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function asStream(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function getContentType(): string
    {
        return $this->response->getHeaderLine('Content-Type');
    }
}
