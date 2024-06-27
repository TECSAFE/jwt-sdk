<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CachedJWKLoader implements JWKLoaderInterface
{
    public function __construct(
        private JWKLoaderInterface $loader,
        private CacheInterface $cache
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function getJWK(string $url, int $ttl): array
    {
        $cacheKey = \md5($url . $ttl);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $jwk = $this->loader->getJWK($url, $ttl);

        $this->cache->set($cacheKey, $jwk, $ttl);

        return $jwk;
    }
}

