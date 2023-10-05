<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Response;

final class ApplicantInfoResponse
{
    /**
     * @var array
     */
    private $applicantInfo;

    public function __construct(array $applicantInfo)
    {
        $this->applicantInfo = $applicantInfo;
    }

    public function asArray(): array
    {
        return $this->applicantInfo;
    }
}
