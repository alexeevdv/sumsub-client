<?php

declare(strict_types=1);

namespace alexeevdv\SumSub\Request;

final class InspectionChecksRequest
{
    /**
     * @var string
     */
    private $inspectionId;

    public function __construct(string $inspectionId)
    {
        $this->inspectionId = $inspectionId;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }
}
