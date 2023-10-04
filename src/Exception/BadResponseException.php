<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Exception;

use Psr\Http\Message\ResponseInterface;
use Throwable;

final class BadResponseException extends \Exception implements Exception
{
    private const MAX_BODY_LENGTH = 1024;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response, Throwable $previous = null)
    {
        $this->response = $response;

        $message = (string) json_encode(
            [
                'statusCode' => $response->getStatusCode(),
                'body' => mb_substr($response->getBody()->getContents(), 0, self::MAX_BODY_LENGTH),
            ]
        );

        parent::__construct($message, 0, $previous);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
