<?php

declare(strict_types=1);

namespace alexeevdv\SumSub;

use alexeevdv\SumSub\Exception\Exception;
use alexeevdv\SumSub\Request\AccessTokenRequest;
use alexeevdv\SumSub\Request\ApplicantDataRequest;
use alexeevdv\SumSub\Request\ApplicantStatusRequest;
use alexeevdv\SumSub\Request\DocumentImageRequest;
use alexeevdv\SumSub\Request\InspectionChecksRequest;
use alexeevdv\SumSub\Request\ResetApplicantRequest;
use alexeevdv\SumSub\Response\AccessTokenResponse;
use alexeevdv\SumSub\Response\ApplicantDataResponse;
use alexeevdv\SumSub\Response\ApplicantStatusResponse;
use alexeevdv\SumSub\Response\DocumentImageResponse;
use alexeevdv\SumSub\Response\InspectionChecksResponse;

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
