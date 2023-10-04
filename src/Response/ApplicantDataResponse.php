<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Response;

final class ApplicantDataResponse
{
    /**
     * @var array
     */
    private $applicantData;

    public function __construct(array $applicantData)
    {
        $this->applicantData = $applicantData;
    }

    public function asArray(): array
    {
        return $this->applicantData;
    }
}
