<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class DocumentImageResponse
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
