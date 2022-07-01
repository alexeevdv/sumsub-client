<?php

namespace alexeevdv\SumSub\Request;

final class DocumentImagesRequest
{
    /**
     * @var string
     */
    private $inspectionId;

    /**
     * @var string
     */
    private $imageId;

    /**
     * @param $inspectionId
     * @param $imageId
     */
    public function __construct($inspectionId, $imageId)
    {
        $this->inspectionId = $inspectionId;
        $this->imageId = $imageId;
    }

    /**
     * @return string
     */
    public function getInspectionId()
    {
        return $this->inspectionId;
    }

    /**
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }
}
