<?php

namespace tests\unit\Request;

use alexeevdv\SumSub\Request\RequestSigner;
use Codeception\Test\Unit;
use GuzzleHttp\Psr7\Request;

final class RequestSignerTest extends Unit
{
    public function testRequestSignatureWithoutQueryParams(): void
    {
        $timeFunction = static function (): int {
            return 1599219016;
        };

        $signer = new RequestSigner(
            'tst:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad',
            'Hej2ch71kG2kTd1iIUDZFNsO5C1lh5Gq',
            $timeFunction
        );

        $request = new Request('POST', '/resources/accessTokens', [], '{"userId": "123"}');
        $request = $signer->sign($request);

        self::assertTrue($request->hasHeader('X-App-Token'));
        self::assertSame(
            ['tst:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad'],
            $request->getHeader('X-App-Token')
        );

        self::assertTrue($request->hasHeader('X-App-Access-Ts'));
        self::assertSame(['1599219016'], $request->getHeader('X-App-Access-Ts'));

        self::assertTrue($request->hasHeader('X-App-Access-Sig'));
        self::assertSame(
            ['600e8a7e7d46253d468afed77a578e0e89daef2dfd580161d86d7b949b7cb4c1'],
            $request->getHeader('X-App-Access-Sig')
        );
    }

    public function testRequestSignatureWithQueryParams(): void
    {
        $timeFunction = static function (): int {
            return 1599219016;
        };

        $signer = new RequestSigner(
            'tst:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad',
            'Hej2ch71kG2kTd1iIUDZFNsO5C1lh5Gq',
            $timeFunction
        );

        $request = new Request('POST', '/resources/accessTokens?name=value', [], '{"userId": "123"}');
        $request = $signer->sign($request);

        self::assertTrue($request->hasHeader('X-App-Token'));
        self::assertSame(
            ['tst:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad'],
            $request->getHeader('X-App-Token')
        );

        self::assertTrue($request->hasHeader('X-App-Access-Ts'));
        self::assertSame(['1599219016'], $request->getHeader('X-App-Access-Ts'));

        self::assertTrue($request->hasHeader('X-App-Access-Sig'));
        self::assertSame(
            ['3f559b485473b32b8e4d0a422fa6201fdb4839e4d23a66d2fb24c97b7baf6a12'],
            $request->getHeader('X-App-Access-Sig')
        );
    }

    public function testRequestSignatureWithDefaultTimeFunction(): void
    {
        $signer = new RequestSigner(
            'tst:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad',
            'Hej2ch71kG2kTd1iIUDZFNsO5C1lh5Gq'
        );

        $request = new Request('POST', '/resources/accessTokens', [], '{"userId": "123"}');
        $request = $signer->sign($request);

        self::assertTrue($request->hasHeader('X-App-Token'));
        self::assertTrue($request->hasHeader('X-App-Access-Ts'));
        self::assertTrue($request->hasHeader('X-App-Access-Sig'));
    }
}
