<?php

declare(strict_types=1);

namespace tests\unit\Exception;

use Codeception\Test\Unit;
use FaritSlv\SumSub\Exception\TransportException;

final class TransportExceptionTest extends Unit
{
    public function testPreviousExceptionWrapped(): void
    {
        $exception = new \Exception('Test', 111);
        $transportException = new TransportException($exception);

        self::assertSame($exception->getMessage(), $transportException->getMessage());
        self::assertSame($exception->getCode(), $transportException->getCode());
        self::assertSame($exception, $transportException->getPrevious());
    }
}
