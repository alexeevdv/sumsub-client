<?php

declare(strict_types=1);

namespace alexeevdv\SumSub\Request;

final class ApplicantDataRequest
{
    /**
     * @var string|null
     */
    private $applicantId;

    /**
     * @var string|null
     */
    private $externalUserId;

    /**
     * @param string|null $applicantId
     * @param string|null $externalUserId
     */
    public function __construct($applicantId, $externalUserId = null)
    {
        if ($applicantId === null && $externalUserId === null) {
            throw new \InvalidArgumentException('Applicant ID and External user ID can not be both null.');
        }
        $this->applicantId = $applicantId;
        $this->externalUserId = $externalUserId;
    }

    /**
     * @return string|null
     */
    public function getApplicantId()
    {
        return $this->applicantId;
    }

    /**
     * @return string|null
     */
    public function getExternalUserId()
    {
        return $this->externalUserId;
    }
}
