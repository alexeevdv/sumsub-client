<?php

declare(strict_types=1);

namespace FaritSlv\SumSub;

use FaritSlv\SumSub\Exception\Exception;
use FaritSlv\SumSub\Request\AccessTokenRequest;
use FaritSlv\SumSub\Request\ApplicantDataRequest;
use FaritSlv\SumSub\Request\ApplicantInfoRequest;
use FaritSlv\SumSub\Request\ApplicantRequest;
use FaritSlv\SumSub\Request\ApplicantStatusPendingRequest;
use FaritSlv\SumSub\Request\DocumentImageRequest;
use FaritSlv\SumSub\Request\InspectionChecksRequest;
use FaritSlv\SumSub\Response\AccessTokenResponse;
use FaritSlv\SumSub\Response\ApplicantDataResponse;
use FaritSlv\SumSub\Response\DocumentImageResponse;
use FaritSlv\SumSub\Response\InspectionChecksResponse;

interface ClientInterface
{
    /**
     * Get access token for SDKs
     *
     * @see https://developers.cyberity.ru/api-reference/#access-tokens-for-sdks
     * @throws Exception
     */
    public function getAccessToken(AccessTokenRequest $request): AccessTokenResponse;

    /**
     * Get applicant data
     *
     * @see https://developers.cyberity.ru/api-reference/#getting-applicant-data
     * @throws Exception
     */
    public function getApplicantData(ApplicantDataRequest $request): ApplicantDataResponse;

    /**
     * Resetting an applicant
     *
     * @see https://developers.cyberity.ru/api-reference/#resetting-an-applicant
     * @throws Exception
     */
    public function resetApplicant(ApplicantRequest $request): void;

    /**
     * Adding an ID document
     *
     * @see https://developers.cyberity.ru/api-reference/#requesting-an-applicant-check
     * @throws Exception
     */
    public function getApplicantStatusPending(ApplicantStatusPendingRequest $request): void;

    /**
     * Adding an ID document
     *
     * @see https://developers.cyberity.ru/api-reference/#adding-an-id-document
     * @throws Exception
     */
    public function getApplicantInfo(ApplicantInfoRequest $request): ApplicantDataResponse;

    /**
     * Get applicant status
     *
     * @see https://developers.cyberity.ru/api-reference/#getting-applicant-status-api
     * @throws Exception
     */
    public function getApplicantStatus(ApplicantRequest $request): ApplicantDataResponse;

    /**
     * Get inspection checks
     *
     * @throws Exception
     */
    public function getInspectionChecks(InspectionChecksRequest $request): InspectionChecksResponse;

    /**
     * Get document images
     *
     * @see https://developers.cyberity.ru/api-reference/#getting-document-images
     * @throws Exception
     */
    public function getDocumentImage(DocumentImageRequest $request): DocumentImageResponse;
}
