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
     * A name of the level configured in the dashboard.
     * @var string
     */
    private $levelName;

    /**
     * Lifespan of a token in seconds. Default value is equal to 10 mins.
     * @var int|null
     */
    private $ttlInSecs;

    /**
     * AccessTokenRequest constructor.
     * @param string $userId
     * @param string $levelName
     * @param int|null $ttlInSecs
     */
    public function __construct($userId, $levelName, $ttlInSecs = null)
    {
        $this->userId = $userId;
        $this->levelName = $levelName;
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
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @return int|null
     */
    public function getTtlInSecs()
    {
        return $this->ttlInSecs;
    }
}
