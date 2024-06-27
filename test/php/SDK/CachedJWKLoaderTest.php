<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Tecsafe\OFCP\JWT\SDK\CachedJWKLoader;
use Tecsafe\OFCP\JWT\SDK\JWKLoader;

#[CoversClass(CachedJWKLoader::class)]
#[UsesClass(JWKLoader::class)]
class CachedJWKLoaderTest extends TestCase
{
    public function testCanIntegrateOriginalLoaderWithCache(): void
    {

        $responses = [
            new JsonMockResponse(['foo' => 'bar']),
            new JsonMockResponse(['foo' => 'baz']),
        ];

        $jwkLoader = new JWKLoader(new Psr18Client(new MockHttpClient($responses)), new Psr17Factory());

        $cachedLoader = new CachedJWKLoader($jwkLoader, new Psr16Cache(new ArrayAdapter()));

        $nonCachedJwk = $cachedLoader->getJWK('http://foo.bar', 42);

        $this->assertEquals(['foo' => 'bar'], $nonCachedJwk);

        $cachedJwl = $cachedLoader->getJWK('http://foo.bar', 42);

        $this->assertEquals(['foo' => 'bar'], $cachedJwl);

        $newNonCachedJwk = $cachedLoader->getJWK('http://foo.baz', 42);

        $this->assertEquals(['foo' => 'baz'], $newNonCachedJwk);
    }
}
