<?php

declare(strict_types=1);

namespace tests\unit\Exception;

use alexeevdv\SumSub\Exception\BadResponseException;
use Codeception\Test\Unit;
use GuzzleHttp\Psr7\Response;

final class BadResponseExceptionTest extends Unit
{
    public function testResponseIsStoredInException(): void
    {
        $response = new Response(418, [], 'I am a teapot!');
        $exception = new BadResponseException($response);

        self::assertSame($response, $exception->getResponse());
        self::assertSame('{"statusCode":418,"body":"I am a teapot!"}', $exception->getMessage());
    }
}
