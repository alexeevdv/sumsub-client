<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

use Psr\Http\Message\StreamInterface;

final class ApplicantInfoRequest
{
    /**
     * @var string
     */
    private $applicantId;

    /**
     * @var StreamInterface
     */
    private $postData;

    /**
     * @var bool
     */
    private $returnDocWarnings;

    public function __construct(string $applicantId, StreamInterface $postData, bool $returnDocWarnings = false)
    {
        $this->applicantId = $applicantId;
        $this->postData = $postData;
        $this->returnDocWarnings = $returnDocWarnings;
    }

    public function getApplicantId(): string
    {
        return $this->applicantId;
    }

    public function getPostData(): StreamInterface
    {
        return $this->postData;
    }

    public function isReturnDocWarnings(): bool
    {
        return $this->returnDocWarnings;
    }
}
