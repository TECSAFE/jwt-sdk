<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Tecsafe\OFCP\JWT\SDK\JWKLoader;

#[CoversClass(JWKLoader::class)]
class JWKLoaderTest extends TestCase
{
    public function testCanIntegrateClient(): void
    {
        $responses = [
            new JsonMockResponse(['foo' => 'bar']),
            new JsonMockResponse(['foo' => 'baz']),
        ];

        $jwkLoader = new JWKLoader(new Psr18Client(new MockHttpClient($responses)), new Psr17Factory());

        $jwk = $jwkLoader->getJWK();

        $this->assertEquals(['foo' => 'bar'], $jwk);

        $jwk = $jwkLoader->getJWK();
        $this->assertEquals(['foo' => 'baz'], $jwk);
    }
}
