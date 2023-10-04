<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

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

    public function __construct(?string $applicantId, ?string $externalUserId = null)
    {
        if ($applicantId === null && $externalUserId === null) {
            throw new \InvalidArgumentException('Applicant ID and External user ID can not be both null.');
        }
        $this->applicantId = $applicantId;
        $this->externalUserId = $externalUserId;
    }

    public function getApplicantId(): ?string
    {
        return $this->applicantId;
    }

    public function getExternalUserId(): ?string
    {
        return $this->externalUserId;
    }
}
