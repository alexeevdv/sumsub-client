<?php

namespace alexeevdv\SumSub\Exception;

use Throwable;

final class TransportException extends \Exception implements Exception
{
    public function __construct(Throwable $previous)
    {
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
    }
}
