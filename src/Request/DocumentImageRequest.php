<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Request;

final class DocumentImageRequest
{
    /**
     * @var string
     */
    private $inspectionId;

    /**
     * @var string
     */
    private $imageId;

    public function __construct(string $inspectionId, string $imageId)
    {
        $this->inspectionId = $inspectionId;
        $this->imageId = $imageId;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }
}
