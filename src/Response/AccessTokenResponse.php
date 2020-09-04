<?php

namespace alexeevdv\SumSub\Response;

final class AccessTokenResponse
{
    /**
     * A newly generated access token for an applicant.
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param string $token
     * @param string $userId
     */
    public function __construct($token, $userId)
    {
        $this->token = $token;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
