<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

final class ApplicantStatusPendingRequest
{
    /**
     * @var string
     */
    private $applicantId;

    /**
     * @var string|null
     */
    private $reason;

    /**
     * @var string|null
     */
    private $reasonCode;

    public function __construct(string $applicantId, ?string $reason = null, ?string $reasonCode = null)
    {
        $this->applicantId = $applicantId;
        $this->reason = $reason;
        $this->reasonCode = $reasonCode;
    }

    public function getApplicantId(): string
    {
        return $this->applicantId;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getReasonCode(): ?string
    {
        return $this->reasonCode;
    }
}
