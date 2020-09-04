<?php

namespace alexeevdv\SumSub\Request;

use Psr\Http\Message\RequestInterface;

interface RequestSignerInterface
{
    public function sign(RequestInterface $request): RequestInterface;
}
