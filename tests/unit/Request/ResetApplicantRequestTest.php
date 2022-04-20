<?php

namespace tests\unit\Request;

use alexeevdv\SumSub\Request\ResetApplicantRequest;
use Codeception\Test\Unit;

final class ResetApplicantRequestTest extends Unit
{
    public function testApplicantIdCannotBeNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ResetApplicantRequest(null);
    }
}
