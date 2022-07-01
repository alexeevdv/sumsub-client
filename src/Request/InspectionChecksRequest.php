<?php

namespace alexeevdv\SumSub\Request;

final class InspectionChecksRequest
{
    /**
     * @var string
     */
    private $inspectionId;


    /**
     * @param $inspectionId
     */
    public function __construct($inspectionId)
    {
        $this->inspectionId = $inspectionId;
    }

    /**
     * @return string
     */
    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }
}
