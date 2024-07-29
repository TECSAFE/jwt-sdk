<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tecsafe\OFCP\JWT\SDK\JWTParser;
use PHPUnit\Framework\TestCase;

use function method_exists;
use function file_get_contents;
use function json_decode;

#[CoversClass(JWTParser::class)]
class JWTParserTest extends TestCase
{
    private const JWT_INTERNAL_RAW = "JwtInternal.json.jwt";
    private const JWT_SALES_CHANNEL_RAW = "JwtSalesChannel.json.jwt";
    private const JWT_CUSTOMER_RAW = "JwtCustomer.json.jwt";
    private const JWT_JWKS_JSON = "jwks.json";
    private const JWT_CUSTOMER_JSON = "JwtCustomer.json";
    private const JWT_INTERNAL_JSON = "JwtInternal.json";
    private const JWT_SALES_CHANNEL_JSON = "JwtSalesChannel.json";

    /**
     * Test: Parse the SalesChannel JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testParseSalesChannelJwk(): void
    {
        $jwtSalesChannel = JWTParser::parseSalesChannelJwt($this->getSalesChannel(true), $this->getJwks());
        $this->assertIsNumeric($jwtSalesChannel->getExp());
        $this->assertIsNumeric($jwtSalesChannel->getIat());
        $this->assertEquals($this->getSalesChannel()['exp'], $jwtSalesChannel->getExp());
        $this->assertEquals($this->getSalesChannel()['iat'], $jwtSalesChannel->getIat());
        $this->assertEquals($this->getSalesChannel()['iss'], $jwtSalesChannel->getIss());
        $this->assertEquals($this->getSalesChannel()['nbf'], $jwtSalesChannel->getNbf());
        $this->assertEquals($this->getSalesChannel()['sub'], $jwtSalesChannel->getSub());
        $this->assertEquals($this->getSalesChannel()['type'], $jwtSalesChannel->getType());
        $this->assertObjectHasProperty('meta', $jwtSalesChannel);
    }

    /**
     * Test: Parse the Internal JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testParseInternalJwk(): void
    {
        $jwtInternal = JWTParser::parseInternalJwt($this->getInternal(true), $this->getJwks());
        $this->assertIsNumeric($jwtInternal->getExp());
        $this->assertIsNumeric($jwtInternal->getIat());
        $this->assertEquals($this->getInternal()['exp'], $jwtInternal->getExp());
        $this->assertEquals($this->getInternal()['iat'], $jwtInternal->getIat());
        $this->assertEquals($this->getInternal()['iss'], $jwtInternal->getIss());
        $this->assertObjectHasProperty('targetServiceId', $jwtInternal->getMeta());
        $this->assertEquals(
            $this->getInternal()['meta']['targetServiceId'],
            $jwtInternal->getMeta()->getTargetServiceId()
        );
        $this->assertEquals($this->getInternal()['nbf'], $jwtInternal->getNbf());
        $this->assertEquals($this->getInternal()['sub'], $jwtInternal->getSub());
        $this->assertEquals($this->getInternal()['type'], $jwtInternal->getType());
    }

    /**
     * Test: Parse the Customer JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testParseCustomerJwk(): void
    {
        $jwtCustomer = JWTParser::parseCustomerJwt($this->getCustomer(true), $this->getJwks());
        $this->assertEquals('customer', $jwtCustomer->getType());
        $meta = $jwtCustomer->getMeta();

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroup', $meta);
        $this->assertTrue(method_exists($meta, 'getSalesChannelId'), 'Class does not have method getSalesChannelId');
        $this->assertTrue(method_exists($meta, 'getCustomerGroup'), 'Class does not have method getCustomerGroup');

        $this->assertEquals($this->getCustomer()['exp'], $jwtCustomer->getExp());
        $this->assertIsNumeric($jwtCustomer->getExp());
        $this->assertIsNumeric($jwtCustomer->getIat());
        $this->assertEquals($this->getCustomer()['iat'], $jwtCustomer->getIat());
        $this->assertEquals($this->getCustomer()['iss'], $jwtCustomer->getIss());
        $this->assertEquals(
            $this->getCustomer()['meta']['customerGroup'],
            $jwtCustomer->getMeta()->getCustomerGroup()
        );
        $this->assertEquals(
            $this->getCustomer()['meta']['salesChannelId'],
            $jwtCustomer->getMeta()->getSalesChannelId()
        );
        $this->assertEquals($this->getCustomer()['nbf'], $jwtCustomer->getNbf());
        $this->assertEquals($this->getCustomer()['sub'], $jwtCustomer->getSub());
        $this->assertEquals($this->getCustomer()['type'], $jwtCustomer->getType());
    }

    /**
     * Test: Parse the Base JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testBase(): void
    {
        $base = JWTParser::parseBaseJwt($this->getBaseJWT(true), $this->getJwks());
        $this->assertIsNumeric($base->getExp());
        $this->assertIsNumeric($base->getIat());
        $this->assertEquals($this->getBaseJWT()['exp'], $base->getExp());
        $this->assertEquals($this->getBaseJWT()['iat'], $base->getIat());
        $this->assertEquals($this->getBaseJWT()['iss'], $base->getIss());
        // Missing 'meta' in the Class, no getter available
        $this->assertEquals($this->getBaseJWT()['nbf'], $base->getNbf());
        $this->assertEquals($this->getBaseJWT()['sub'], $base->getSub());
        $this->assertEquals($this->getBaseJWT()['type'], $base->getType());
    }

    /**
     * Get the path to the keys (and optional including the filename)
     * @param string|null $fileName
     * @return string
     */
    public function getKeysPath(?string $fileName): string
    {
        if (!is_null($fileName)) {
            return sprintf("%s/%s", $this->getKeysPath(null), $fileName);
        }
        return __DIR__ . '/../../example/keys/';
    }

    /**
     * Get a key, get its content and decode the json
     * @param string|null $fileName
     * @return array
     */
    private function getKeyAndJsonDecode(?string $fileName): array
    {
        return json_decode($this->getKeyContent($fileName), true);
    }

    /**
     * Get the content of a key
     * @param string|null $fileName
     * @return string
     */
    private function getKeyContent(?string $fileName): string
    {
        return file_get_contents($this->getKeysPath($fileName));
    }

    /**
     * Get the JWKS
     * @return array
     */
    private function getJwks(): array
    {
        return $this->getKeyAndJsonDecode(self::JWT_JWKS_JSON);
    }

    /**
     * Get the customer (raw or json)
     * @param bool $rawJwt
     * @return string|array
     */
    private function getCustomer(bool $rawJwt = false): string|array
    {
        if ($rawJwt) {
            return $this->getKeyContent(self::JWT_CUSTOMER_RAW);
        } else {
            return $this->getKeyAndJsonDecode(self::JWT_CUSTOMER_JSON);
        }
    }

    /**
     * Get the internal (raw or json)
     * @param bool $rawJwt
     * @return string|array
     */
    private function getInternal(bool $rawJwt = false): string|array
    {
        if ($rawJwt) {
            return $this->getKeyContent(self::JWT_INTERNAL_RAW);
        } else {
            return $this->getKeyAndJsonDecode(self::JWT_INTERNAL_JSON);
        }
    }

    /**
     * Get the sales channel (raw or json)
     * @param bool $rawJwt
     * @return string|array
     */
    private function getSalesChannel(bool $rawJwt = false): string|array
    {
        if ($rawJwt) {
            return $this->getKeyContent(self::JWT_SALES_CHANNEL_RAW);
        } else {
            return $this->getKeyAndJsonDecode(self::JWT_SALES_CHANNEL_JSON);
        }
    }

    /**
     * Get the base JWT (raw or json)
     * @param bool $rawJwt
     * @return string|array
     */
    private function getBaseJWT(bool $rawJwt = false): string|array
    {
        if ($rawJwt) {
            return $this->getKeyContent(self::JWT_INTERNAL_RAW);
        } else {
            return $this->getKeyAndJsonDecode(self::JWT_INTERNAL_JSON);
        }
    }
}
