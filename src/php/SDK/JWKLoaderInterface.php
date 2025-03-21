<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

interface JWKLoaderInterface
{
    public function getJWK(string $url, int $ttl): array;
}
