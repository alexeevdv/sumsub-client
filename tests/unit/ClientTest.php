<?php

declare(strict_types=1);

namespace tests\unit;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use FaritSlv\SumSub\Client;
use FaritSlv\SumSub\Exception\BadResponseException;
use FaritSlv\SumSub\Exception\TransportException;
use FaritSlv\SumSub\Request\AccessTokenRequest;
use FaritSlv\SumSub\Request\ApplicantDataRequest;
use FaritSlv\SumSub\Request\ApplicantInfoRequest;
use FaritSlv\SumSub\Request\ApplicantRequest;
use FaritSlv\SumSub\Request\ApplicantStatusPendingRequest;
use FaritSlv\SumSub\Request\DocumentImageRequest;
use FaritSlv\SumSub\Request\RequestSignerInterface;
use GuzzleHttp\Psr7\MultipartStream;
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
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('https', $request->getUri()->getScheme());
                self::assertSame('api.cyberity.ru', $request->getUri()->getHost());
                self::assertSame('/resources/accessTokens', $request->getUri()->getPath());
                self::assertSame('userId=123456&levelName=test-level', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'token' => '654321',
                    'userId' => '123456',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $accessTokenResponse = $client->getAccessToken(
            new AccessTokenRequest('123456', 'test-level')
        );

        // Assert
        self::assertSame('654321', $accessTokenResponse->getToken());
        self::assertSame('123456', $accessTokenResponse->getUserId());
    }

    public function testGetAccessTokenWithTtlInSeconds(): void
    {
        // Arrange
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

        // Act
        $accessTokenResponse = $client->getAccessToken(
            new AccessTokenRequest('123456', 'test-level', 3600)
        );

        // Assert
        self::assertSame('654321', $accessTokenResponse->getToken());
        self::assertSame('123456', $accessTokenResponse->getUserId());
    }

    public function testGetAccessTokenWhenRequestFailed(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                throw new class() extends \Exception implements ClientExceptionInterface {
                };
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(TransportException::class);
        $client->getAccessToken(new AccessTokenRequest('123456', 'test-level'));
    }

    public function testGetAccessTokenWhenResponseCodeIsNot200(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(500, [], 'Smth went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getAccessToken(new AccessTokenRequest('123456', 'test-level'));
    }

    public function testGetApplicantDataByApplicantId(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'a' => 'b',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $applicantDataResponse = $client->getApplicantData(new ApplicantDataRequest('123456'));

        // Assert
        self::assertSame([
            'a' => 'b',
        ], $applicantDataResponse->asArray());
    }

    public function testGetApplicantDataByExternalUserId(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/-;externalUserId=654321/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'a' => 'b',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $applicantDataResponse = $client->getApplicantData(new ApplicantDataRequest(null, '654321'));

        // Assert
        self::assertSame([
            'a' => 'b',
        ], $applicantDataResponse->asArray());
    }

    public function testGetApplicantDataWhenResponseCodeIsNot200(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/one', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getApplicantData(new ApplicantDataRequest('123456'));
    }

    public function testGetApplicantDataWhenCanNotDecodeResponse(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getApplicantData(new ApplicantDataRequest('123456'));
    }

    public function testResetApplicantIsOk(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/reset', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'ok' => 1,
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $client->resetApplicant(new ApplicantRequest('123456'));

        // Assert
    }

    public function testResetApplicantIsNotOk(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/reset', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'ok' => 0,
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->resetApplicant(new ApplicantRequest('123456'));
    }

    /**
     * @throws TransportException
     */
    public function testResetApplicantWhenResponseCodeIsNot200(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/reset', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->resetApplicant(new ApplicantRequest('123456'));
    }

    public function testResetApplicantWhenCanNotDecodeResponse(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->resetApplicant(new ApplicantRequest('123456'));
    }

    public function testGetApplicantStatus(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/requiredIdDocsStatus', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'a' => 'b',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $applicantStatusResponse = $client->getApplicantStatus(new ApplicantRequest('123456'));

        // Assert
        self::assertSame([
            'a' => 'b',
        ], $applicantStatusResponse->asArray());
    }

    public function testGetApplicantStatusPending(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/status/pending', $request->getUri()->getPath());
                self::assertSame('reason=someReason&reasonCode=wlCheck', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'ok' => 1,
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $client->getApplicantStatusPending(new ApplicantStatusPendingRequest('123456', 'someReason', 'wlCheck'));
    }

    public function testGetApplicantStatusPendingIsNotOk(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/status/pending', $request->getUri()->getPath());
                self::assertSame('reason=someReason&reasonCode=wlCheck', $request->getUri()->getQuery());

                return new Response(200, [], json_encode([
                    'ok' => 0,
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $this->expectException(BadResponseException::class);
        $client->getApplicantStatusPending(new ApplicantStatusPendingRequest('123456', 'someReason', 'wlCheck'));
    }

    public function testGetApplicantInfo(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/info/idDoc', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());
                self::assertSame(1, (int) $request->getHeader('X-Return-Doc-Warnings')[0]);
                self::assertSame('multipart/form-data', $request->getHeader('Content-Type')[0]);

                return new Response(200, [], json_encode([
                    'idDocType' => 'PASSPORT',
                    'country' => 'GBR',
                    'issuedDate' => '2015-01-02',
                    'number' => '40111234567',
                    'dob' => '2000-02-01',
                    'placeOfBirth' => 'London',
                ]));
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());
        $elements = [
            [
                'name' => 'metadata',
                'contents' => json_encode([
                    'idDocType' => 'PASSPORT',
                    'country' => 'GBR',
                    'number' => '40111234567',
                    'issuedDate' => '2015-01-02',
                    'dob' => '2000-02-01',
                    'placeOfBirth' => 'London',
                ]),
            ],
        ];

        // Act
        $applicantInfo = $client->getApplicantInfo(new ApplicantInfoRequest('123456', new MultipartStream($elements), true));
    }

    public function testGetApplicantStatusWhenResponseCodeIsNot200(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/applicants/123456/requiredIdDocsStatus', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getApplicantStatus(new ApplicantRequest('123456'));
    }

    public function testGetApplicantStatusWhenCanNotDecodeResponse(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                return new Response(200, [], 'Not a JSON string');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getApplicantStatus(new ApplicantRequest('123456'));
    }

    public function testGetDocumentImages(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/inspections/123456/resources/654321', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(200, [
                    'Content-Type' => 'text/plain',
                ], 'contents');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act
        $applicantStatusResponse = $client->getDocumentImage(new DocumentImageRequest('123456', '654321'));

        // Assert
        self::assertSame('contents', (string) $applicantStatusResponse->asStream());
        self::assertSame('text/plain', $applicantStatusResponse->getContentType());
    }

    public function testGetDocumentImagesWhenResponseCodeIsNot200(): void
    {
        // Arrange
        /** @var ClientInterface $httpClient */
        $httpClient = $this->makeEmpty(ClientInterface::class, [
            'sendRequest' => Expected::once(static function (RequestInterface $request): ResponseInterface {
                self::assertSame('/resources/inspections/123456/resources/654321', $request->getUri()->getPath());
                self::assertSame('', $request->getUri()->getQuery());

                return new Response(500, [], 'Something went wrong');
            }),
        ]);

        $client = new Client($httpClient, $this->getRequestFactory(), $this->getRequestSigner());

        // Act && Assert
        $this->expectException(BadResponseException::class);
        $client->getDocumentImage(new DocumentImageRequest('123456', '654321'));
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
