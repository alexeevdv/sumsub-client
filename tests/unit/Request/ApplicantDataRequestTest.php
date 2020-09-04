<?php

namespace tests\unit\Request;

use alexeevdv\SumSub\Request\ApplicantDataRequest;
use Codeception\Test\Unit;

final class ApplicantDataRequestTest extends Unit
{
    public function testAtLeastOneUserIdIsRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ApplicantDataRequest(null, null);
    }
}
