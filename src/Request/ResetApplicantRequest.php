<?php

namespace alexeevdv\SumSub\Request;

final class ResetApplicantRequest
{
    /**
     * @var string|null
     */
    private $applicantId;

    /**
     * @param string|null $applicantId
     */
    public function __construct($applicantId)
    {
        $this->applicantId = $applicantId;
    }

    /**
     * @return string|null
     */
    public function getApplicantId()
    {
        return $this->applicantId;
    }
}
