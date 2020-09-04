<?php

namespace alexeevdv\SumSub;

use alexeevdv\SumSub\Exception\BadResponseException;
use alexeevdv\SumSub\Exception\TransportException;
use alexeevdv\SumSub\Request\AccessTokenRequest;
use alexeevdv\SumSub\Request\ApplicantDataRequest;
use alexeevdv\SumSub\Request\RequestSignerInterface;
use alexeevdv\SumSub\Response\AccessTokenResponse;
use alexeevdv\SumSub\Response\ApplicantDataResponse;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

    /**
     * @param HttpClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param RequestSignerInterface $requestSigner
     * @param string $baseUrl
     */
    public function __construct(
        $httpClient,
        $requestFactory,
        $requestSigner,
        $baseUrl = self::PRODUCTION_BASE_URI
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
        $url = $this->baseUrl . '/resources/accessTokens?userId=' . $request->getUserId();
        if ($request->getTtlInSecs() !== null) {
            $url .= '&ttlInSecs=' . $request->getTtlInSecs();
        }

        $httpRequest = $this->createApiRequest('POST', $url);
        $httpResponse = $this->sendApiRequest($httpRequest);

        if ($httpResponse->getStatusCode() !== 200) {
            throw new BadResponseException($httpResponse);
        }

        $decodedResponse = $this->decodeResponse($httpResponse);

        return new AccessTokenResponse($decodedResponse['token'], $decodedResponse['userId']);
    }

    /**
     * @throws BadResponseException
     * @throws TransportException
     */
    public function getApplicantData(ApplicantDataRequest $request): ApplicantDataResponse
    {
        if ($request->getApplicantId() !== null) {
            $url = $this->baseUrl . '/resources/applicants/' . $request->getApplicantId() . '/one';
        } else {
            $url = $this->baseUrl . '/resources/applicants/-;externalUserId=' . $request->getExternalUserId() . '/one';
        }

        $httpRequest = $this->createApiRequest('GET', $url);
        $httpResponse = $this->sendApiRequest($httpRequest);

        if ($httpResponse->getStatusCode() !== 200) {
            throw new BadResponseException($httpResponse);
        }

        return new ApplicantDataResponse($this->decodeResponse($httpResponse));
    }

    private function createApiRequest($method, $uri): RequestInterface
    {
        $httpRequest = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Accept', 'application/json')
        ;
        $httpRequest = $this->requestSigner->sign($httpRequest);

        return $httpRequest;
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
