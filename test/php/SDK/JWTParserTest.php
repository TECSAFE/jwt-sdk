<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use PHPUnit\Framework\Attributes\CoversClass;
use Tecsafe\OFCP\JWT\SDK\JWTParser;
use PHPUnit\Framework\TestCase;

#[CoversClass(JWTParser::class)]
class JWTParserTest extends TestCase
{

    /*public function testParseSalesChannelJwk()
    {
    }

    public function testParseJwk()
    {
    }

    public function testParseInternalJwk()
    {
    }*/

    // public function testParseBaseJwk()
    // {
    //     $this->expectException(\PHPModelGenerator\Exception\ErrorRegistryException::class);
    //     $this->expectExceptionMessage('Invalid value for type declined by enum constraint');

    //     JWTParser::parseBaseJwt(self::UNKNOWN_JWT, $this->getJWKS());
    // }

    public function testParseCustomerJwk()
    {
        $jwtCustomer = JWTParser::parseCustomerJwt($this->getCustomerJWT(), $this->getJWKS());
        $this->assertEquals('customer', $jwtCustomer->getType());

        $meta = $jwtCustomer->getMeta();

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroup', $meta);
        $this->assertTrue(\method_exists($meta, 'getSalesChannelId'), 'Class does not have method getSalesChannelId');
        $this->assertTrue(\method_exists($meta, 'getCustomerGroup'), 'Class does not have method getCustomerGroup');


        /*$this->assertEquals(new JwtCustomer_Meta6659a82ea5bb2([
            'salesChannelId' => 'foobar',
            'customerGroup' => 'foobaz',
        ]), $jwtCustomer->getMeta());*/
        $this->assertEquals('api-gateway', $jwtCustomer->getIss());
    }

    private function getJWKS(): array
    {
        return \json_decode(\file_get_contents(__DIR__ . '/../../example/keys/jwks.json'), true);
    }

    private function getCustomerJWT(): string
    {
        return \file_get_contents(__DIR__ . '/../../example/jwt/JwtCustomer.json.jwt');
    }

    private function getInternalJWT(): string
    {
        return \file_get_contents(__DIR__ . '/../../example/jwt/JwtInternal.json.jwt');
    }

    private function getSalesChannelJWT(): string
    {
        return \file_get_contents(__DIR__ . '/../../example/jwt/JwtSalesChannel.json.jwt');
    }

    private function getBaseJWT(): string
    {
        return \file_get_contents(__DIR__ . '/../../example/jwt/invalid.jwt');
    }
}
