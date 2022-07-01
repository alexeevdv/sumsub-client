<?php

declare(strict_types=1);

namespace alexeevdv\SumSub\Request;

use Psr\Http\Message\RequestInterface;

interface RequestSignerInterface
{
    public function sign(RequestInterface $request): RequestInterface;
}
