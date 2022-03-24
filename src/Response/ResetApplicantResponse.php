<?php

namespace alexeevdv\SumSub\Response;

final class ResetApplicantResponse
{
    /**
     * @var array
     */
    private $resetStatus;

    public function __construct(array $resetStatus)
    {
        $this->resetStatus = $resetStatus;
    }

    public function asArray(): array
    {
        return $this->resetStatus;
    }
}
