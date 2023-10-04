<?php

declare(strict_types=1);

namespace FaritSlv\SumSub;

use FaritSlv\SumSub\Exception\Exception;
use FaritSlv\SumSub\Request\AccessTokenRequest;
use FaritSlv\SumSub\Request\ApplicantDataRequest;
use FaritSlv\SumSub\Request\ApplicantStatusRequest;
use FaritSlv\SumSub\Request\DocumentImageRequest;
use FaritSlv\SumSub\Request\InspectionChecksRequest;
use FaritSlv\SumSub\Request\ResetApplicantRequest;
use FaritSlv\SumSub\Response\AccessTokenResponse;
use FaritSlv\SumSub\Response\ApplicantDataResponse;
use FaritSlv\SumSub\Response\ApplicantStatusResponse;
use FaritSlv\SumSub\Response\DocumentImageResponse;
use FaritSlv\SumSub\Response\InspectionChecksResponse;

interface ClientInterface
{
    /**
     * Get access token for SDKs
     *
     * @see https://developers.sumsub.com/api-reference/#access-tokens-for-sdks
     * @throws Exception
     */
    public function getAccessToken(AccessTokenRequest $request): AccessTokenResponse;

    /**
     * Get applicant data
     *
     * @see https://developers.sumsub.com/api-reference/#getting-applicant-data
     * @throws Exception
     */
    public function getApplicantData(ApplicantDataRequest $request): ApplicantDataResponse;

    /**
     * Resetting an applicant
     *
     * @see https://developers.sumsub.com/api-reference/#resetting-an-applicant
     * @throws Exception
     */
    public function resetApplicant(ResetApplicantRequest $request): void;

    /**
     * Get applicant status
     *
     * @see https://developers.sumsub.com/api-reference/#getting-applicant-status-api
     * @throws Exception
     */
    public function getApplicantStatus(ApplicantStatusRequest $request): ApplicantStatusResponse;

    /**
     * Get document images
     *
     * @see https://developers.sumsub.com/api-reference/#getting-document-images
     * @throws Exception
     */
    public function getDocumentImage(DocumentImageRequest $request): DocumentImageResponse;

    /**
     * Get inspection checks
     *
     * @throws Exception
     */
    public function getInspectionChecks(InspectionChecksRequest $request): InspectionChecksResponse;
}
