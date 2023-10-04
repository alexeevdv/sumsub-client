<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

use Psr\Http\Message\RequestInterface;

interface RequestSignerInterface
{
    public function sign(RequestInterface $request): RequestInterface;
}
