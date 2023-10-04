<?php

declare(strict_types=1);

namespace FaritSlv\SumSub\Response;

final class InspectionChecksResponse
{
    /**
     * @var array
     */
    private $checksData;

    public function __construct(array $checksData)
    {
        $this->checksData = $checksData;
    }

    public function asArray(): array
    {
        return $this->checksData;
    }
}
