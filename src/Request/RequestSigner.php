<?php

namespace alexeevdv\SumSub\Request;

use Psr\Http\Message\RequestInterface;

final class RequestSigner implements RequestSignerInterface
{
    /**
     * @var string
     */
    private $appToken;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var \Closure
     */
    private $timeFunction;

    /**
     * @param string $appToken
     * @param string $secretKey
     * @param \Closure $timeFunction
     */
    public function __construct($appToken, $secretKey, $timeFunction = null)
    {
        $this->appToken = $appToken;
        $this->secretKey = $secretKey;
        if ($timeFunction === null) {
            $timeFunction = static function (): int {
                return time();
            };
        }
        $this->timeFunction = $timeFunction;
    }

    public function sign(RequestInterface $request): RequestInterface
    {
        $currentTimestamp = call_user_func($this->timeFunction);

        $httpMethod = strtoupper($request->getMethod());
        $url = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        if (strlen($query) > 0) {
            $url .= '?' . $query;
        }

        $signature = hash_hmac(
            'sha256',
            $currentTimestamp . $httpMethod . $url . $request->getBody()->getContents(),
            $this->secretKey
        );

        return $request
            ->withHeader('X-App-Token', $this->appToken)
            ->withHeader('X-App-Access-Ts', $currentTimestamp)
            ->withHeader('X-App-Access-Sig', $signature)
        ;
    }
}
