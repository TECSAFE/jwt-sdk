<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

interface JWKLoaderInterface
{
    /**
     * @return array<mixed>
     */
    public function getJWK(
        string $url,
        int $ttl
    ): array;
}
