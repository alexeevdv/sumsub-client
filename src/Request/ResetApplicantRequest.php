<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

final class ResetApplicantRequest
{
    /**
     * @var string
     */
    private $applicantId;

    public function __construct(string $applicantId)
    {
        $this->applicantId = $applicantId;
    }

    public function getApplicantId(): string
    {
        return $this->applicantId;
    }
}
