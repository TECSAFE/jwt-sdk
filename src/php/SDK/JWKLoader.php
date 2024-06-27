<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class JWKLoader implements JWKLoaderInterface
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory
    ) {}

    /**
     * @throws ClientExceptionInterface
     */
    public function getJWK(
        string $url = "https://api-gateway.tecsafe.example.com/.well-known/jwks",
        int $ttl = 15 * 60,
    ): array
    {
        $request = $this->requestFactory->createRequest('GET', $url);

        $response = $this->httpClient->sendRequest($request);

        return \json_decode($response->getBody()->getContents(), true);
    }
}
