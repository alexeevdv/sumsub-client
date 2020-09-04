<?php

namespace alexeevdv\SumSub\Request;

final class AccessTokenRequest
{
    /**
     * An external user ID which will be bound to the token.
     * @var string
     */
    private $userId;

    /**
     * Lifespan of a token in seconds. Default value is equal to 10 mins.
     * @var int|null
     */
    private $ttlInSecs;

    /**
     * AccessTokenRequest constructor.
     * @param string $userId
     * @param int|null $ttlInSecs
     */
    public function __construct($userId, $ttlInSecs = null)
    {
        $this->userId = $userId;
        $this->ttlInSecs = $ttlInSecs;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function getTtlInSecs()
    {
        return $this->ttlInSecs;
    }
}
