<?php

declare(strict_types=1);

namespace FaritSlv\SumSub;

use FaritSlv\SumSub\Exception\BadResponseException;
use FaritSlv\SumSub\Exception\TransportException;
use FaritSlv\SumSub\Request\AccessTokenRequest;
use FaritSlv\SumSub\Request\ApplicantDataRequest;
use FaritSlv\SumSub\Request\ApplicantInfoRequest;
use FaritSlv\SumSub\Request\ApplicantRequest;
use FaritSlv\SumSub\Request\ApplicantStatusPendingRequest;
use FaritSlv\SumSub\Request\DocumentImageRequest;
use FaritSlv\SumSub\Request\InspectionChecksRequest;
use FaritSlv\SumSub\Request\RequestSignerInterface;
use FaritSlv\SumSub\Response\AccessTokenResponse;
use FaritSlv\SumSub\Response\ApplicantDataResponse;
use FaritSlv\SumSub\Response\DocumentImageResponse;
use FaritSlv\SumSub\Response\InspectionChecksResponse;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Client implements ClientInterface
{
    public const PRODUCTION_BASE_URI = 'https://api.sumsub.com';

    public const STAGING_BASE_URI = 'https://test-api.sumsub.com';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var RequestSignerInterface
     */
    private $requestSigner;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        RequestSignerInterface $requestSigner,
        string $baseUrl = self::PRODUCTION_BASE_URI
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->requestSigner = $requestSigner;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getAccessToken(AccessTokenRequest $request): AccessTokenResponse
    {
        $queryParams = [
            'userId' => $request->getUserId(),
            'levelName' => $request->getLevelName(),
        ];

        if ($request->getTtlInSecs() !== null) {
            $queryParams['ttlInSecs'] = $request->getTtlInSecs();
        }

        $decodedResponse = $this->request(
            'POST',
            '/resources/accessTokens?' . http_build_query($queryParams)
        );

        return new AccessTokenResponse($decodedResponse['token'], $decodedResponse['userId']);
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getApplicantData(ApplicantDataRequest $request): ApplicantDataResponse
    {
        $applicantId = '-;externalUserId=' . $request->getExternalUserId();
        if ($request->getApplicantId() !== null) {
            $applicantId = $request->getApplicantId();
        }

        return new ApplicantDataResponse($this->request(
            'GET',
            '/resources/applicants/' . $applicantId . '/one'
        ));
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function resetApplicant(ApplicantRequest $request): void
    {
        $this->request('POST', '/resources/applicants/' . $request->getApplicantId() . '/reset', true);
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getApplicantStatus(ApplicantRequest $request): ApplicantDataResponse
    {
        return new ApplicantDataResponse($this->request(
            'GET',
            '/resources/applicants/' . $request->getApplicantId() . '/requiredIdDocsStatus'
        ));
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getApplicantStatusPending(ApplicantStatusPendingRequest $request): ApplicantDataResponse
    {
        $url = '/resources/applicants/' . $request->getApplicantId() . '/status/pending';

        $queryParams = [];
        if ($request->getReason() !== null) {
            $queryParams['reason'] = $request->getReason();
        }
        if ($request->getReasonCode() !== null) {
            $queryParams['reasonCode'] = $request->getReasonCode();
        }
        if (count($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }

        return new ApplicantdataResponse($this->request('POST', $url));
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getApplicantInfo(ApplicantInfoRequest $request): ApplicantDataResponse
    {
        $url = '/resources/applicants/' . $request->getApplicantId() . '/info/idDoc';
        $headers = [
            'Content-Type' => 'multipart/form-data',
            'X-Return-Doc-Warnings' => $request->isReturnDocWarnings(),
        ];

        return new ApplicantDataResponse(
            $this->request('POST', $url, false, true, $headers, $request->getPostData())
        );
    }

    public function getDocumentImage(DocumentImageRequest $request): DocumentImageResponse
    {
        return new DocumentImageResponse(
            $this->request(
                'GET',
                sprintf('/resources/inspections/%s/resources/%s', $request->getInspectionId(), $request->getImageId()),
                false,
                false
            )
        );
    }

    public function getInspectionChecks(InspectionChecksRequest $request): InspectionChecksResponse
    {
        return new InspectionChecksResponse($this->request('GET', '/resources/inspections/' . $request->getInspectionId() . '/checks'));
    }

    /**
     * @return array|ResponseInterface
     * @throws TransportException
     * @throws BadResponseException
     */
    private function request(string $method, string $uri, bool $checkOk = false, $asJson = true, array $headers = [], ?StreamInterface $stream = null)
    {
        $request = $this->createApiRequest($method, $this->baseUrl . $uri, $headers);

        if ($stream !== null) {
            $request = $request->withBody($stream);
        }

        $response = $this->sendApiRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new BadResponseException($response);
        }

        if (! $asJson) {
            return $response;
        }

        $result = $this->decodeResponse($response);
        if ($checkOk && ($result['ok'] ?? 0) === 0) {
            throw new BadResponseException($response);
        }

        return $result;
    }

    private function createApiRequest(string $method, string $uri, array $headers = []): RequestInterface
    {
        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Accept', 'application/json');

        foreach ($headers as $key => $header) {
            $request = $request->withHeader($key, $header);
        }

        return $this->requestSigner->sign($request);
    }

    /**
     * @throws TransportException
     */
    private function sendApiRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException($e);
        }
    }

    /**
     * @throws BadResponseException
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        try {
            $result = json_decode($response->getBody()->getContents(), true);
            if ($result === null) {
                throw new \Exception(json_last_error_msg());
            }
            return $result;
        } catch (\Throwable $e) {
            throw new BadResponseException($response, $e);
        }
    }
}
