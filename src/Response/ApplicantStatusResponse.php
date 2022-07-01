<?php

namespace alexeevdv\SumSub\Response;

final class ApplicantStatusResponse
{
    /**
     * @var array
     */
    private $applicantStatus;

    public function __construct(array $applicantStatus)
    {
        $this->applicantStatus = $applicantStatus;
    }

    public function asArray(): array
    {
        return $this->applicantStatus;
    }
}
