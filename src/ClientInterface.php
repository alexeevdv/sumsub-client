<?php

namespace alexeevdv\SumSub;

use alexeevdv\SumSub\Exception\Exception;
use alexeevdv\SumSub\Request\AccessTokenRequest;
use alexeevdv\SumSub\Request\ApplicantDataRequest;
use alexeevdv\SumSub\Request\ResetApplicantRequest;
use alexeevdv\SumSub\Response\AccessTokenResponse;
use alexeevdv\SumSub\Response\ApplicantDataResponse;
use alexeevdv\SumSub\Response\ResetApplicantResponse;

interface ClientInterface
{
    /**
     * Get access token for SDKs
     * @see https://developers.sumsub.com/api-reference/#access-tokens-for-sdks
     * @throws Exception
     */
    public function getAccessToken(AccessTokenRequest $request): AccessTokenResponse;

    /**
     * Get applicant data
     * @see https://developers.sumsub.com/api-reference/#getting-applicant-data
     * @throws Exception
     */
    public function getApplicantData(ApplicantDataRequest $request): ApplicantDataResponse;

    /**
     * Resetting an applicant
     * @see https://developers.sumsub.com/api-reference/#resetting-an-applicant
     * @throws Exception
     */
    public function resetApplicant(ResetApplicantRequest $request): ResetApplicantResponse;
}
