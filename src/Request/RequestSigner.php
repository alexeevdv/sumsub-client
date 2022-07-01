<?php

declare(strict_types=1);

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
     * @var callable
     */
    private $timeFunction;

    public function __construct(string $appToken, string $secretKey, callable $timeFunction = null)
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
        if ($query !== '') {
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
            ->withHeader('X-App-Access-Sig', $signature);
    }
}
