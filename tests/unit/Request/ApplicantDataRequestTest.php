<?php

declare(strict_types=1);

namespace tests\unit\Request;

use Codeception\Test\Unit;
use FaritSlv\SumSub\Request\ApplicantDataRequest;

final class ApplicantDataRequestTest extends Unit
{
    public function testAtLeastOneUserIdIsRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ApplicantDataRequest(null, null);
    }
}
