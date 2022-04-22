<?php

namespace tests\unit;

use alexeevdv\SumSub\Client;
use alexeevdv\SumSub\Exception\BadResponseException;
use alexeevdv\SumSub\Exception\TransportException;
use alexeevdv\SumSub\Request\AccessTokenRequest;
use alexeevdv\SumSub\Request\ApplicantDataRequest;
use alexeevdv\SumSub\Request\ApplicantStatusRequest;
use alexeevdv\SumSub\Request\DocumentImagesRequest;
use alexeevdv\SumSub\Request\RequestSignerInterface;
use alexeevdv\SumSub\Request\ResetApplicantRequest;
use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ClientTest extends Unit
{
    public function testGetAccessTokenWithoutTtlInSeconds(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('https', $request->getUri()->getScheme());
                self::assertSame('api.sumsub.com', $request->getUri()->getHost());
                self::assertSame('/resources/accessTokens', $request->getUri()->getPath());
                self::assertSame('userId=123456&levelName=test-level', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'token' => '654321',
                    'userId' => '123456',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $accessTokenResponse = $client->getAccessToken(
            new AccessTokenRequest('123456', 'test-level')
        );

        self::assertSame('654321', $accessTokenResponse->getToken());
        self::assertSame('123456', $accessTokenResponse->getUserId());
    }

    public function testGetAccessTokenWithTtlInSeconds(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/accessTokens', $request->getUri()->getPath());
                self::assertSame('userId=123456&levelName=test-level&ttlInSecs=3600', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'token' => '654321',
                    'userId' => '123456',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $accessTokenResponse = $client->getAccessToken(
            new AccessTokenRequest('123456', 'test-level', 3600)
        );

        self::assertSame('654321', $accessTokenResponse->getToken());
        self::assertSame('123456', $accessTokenResponse->getUserId());
    }

    public function testGetAccessTokenWhenRequestFailed(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                throw new class () extends \Exception implements ClientExceptionInterface {
                };
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(TransportException::class);
        $client->getAccessToken(new AccessTokenRequest('123456', 'test-level'));
    }

    public function testGetAccessTokenWhenResponseCodeIsNot200(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(500, [], 'Smth went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->getAccessToken(new AccessTokenRequest('123456', 'test-level'));
    }

    public function testGetApplicantDataByApplicantId(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode(['a' => 'b']));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $applicantDataResponse = $client->getApplicantData(new ApplicantDataRequest('123456'));
        self::assertSame(['a' => 'b'], $applicantDataResponse->asArray());
    }

    public function testGetApplicantDataByExternalUserId(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/-;externalUserId=654321/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode(['a' => 'b']));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $applicantDataResponse = $client->getApplicantData(new ApplicantDataRequest(null, '654321'));
        self::assertSame(['a' => 'b'], $applicantDataResponse->asArray());
    }

    public function testGetApplicantDataWhenResponseCodeIsNot200(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->getApplicantData(new ApplicantDataRequest('123456'));
    }

    public function testGetApplicantDataWhenCanNotDecodeResponse(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
         $client->getApplicantData(new ApplicantDataRequest('123456'));
    }

    public function testResetApplicant(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/reset', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode(['ok' => 1]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $client->resetApplicant(new ResetApplicantRequest('123456'));
    }

    public function testResetApplicantWhenResponseCodeIsNot200(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/reset', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->resetApplicant(new ResetApplicantRequest('123456'));
    }

    public function testResetApplicantWhenCanNotDecodeResponse(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->resetApplicant(new ResetApplicantRequest('123456'));
    }

    public function testGetApplicantStatus(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/requiredIdDocsStatus', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode(['a' => 'b']));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $applicantStatusResponse = $client->getApplicantStatus(new ApplicantStatusRequest('123456'));
        self::assertSame(['a' => 'b'], $applicantStatusResponse->asArray());
    }

    public function testGetApplicantStatusWhenResponseCodeIsNot200(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/requiredIdDocsStatus', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->getApplicantStatus(new ApplicantStatusRequest('123456'));
    }

    public function testGetApplicantStatusWhenCanNotDecodeResponse(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->getApplicantStatus(new ApplicantStatusRequest('123456'));
    }

    public function testGetDocumentImages(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/inspections/123456/resources/654321', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, ['Content-Type' => 'text/plain'], 'contents');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $applicantStatusResponse = $client->getDocumentImages(new DocumentImagesRequest('123456', '654321'));

        self::assertSame('contents', (string) $applicantStatusResponse->asStream());
        self::assertSame('text/plain', $applicantStatusResponse->getContentType());
    }

    public function testGetDocumentImagesWhenResponseCodeIsNot200(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/inspections/123456/resources/654321', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        $this->expectException(BadResponseException::class);
        $client->getDocumentImages(new DocumentImagesRequest('123456', '654321'));
    }

    private function getRequestSigner(): RequestSignerInterface
    {
        /** @var RequestSignerInterface $signer */
        $signer = $this->makeEmpty(RequestSignerInterface::class, [
            'sign' => static function (RequestInterface $request): RequestInterface {
                return $request;
            },
        ]);
        return $signer;
    }

    private function getRequestFactory(): RequestFactoryInterface
    {
        /** @var RequestFactoryInterface $factory */
        $factory = $this->makeEmpty(RequestFactoryInterface::class, [
            'createRequest' => static function (string $method, $uri): RequestInterface {
                return new Request($method, $uri);
            },
        ]);
        return $factory;
    }
}
