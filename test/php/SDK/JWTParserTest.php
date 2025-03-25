<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tecsafe\OFCP\JWT\SDK\JWTParser;
use PHPUnit\Framework\TestCase;
use Tecsafe\OFCP\JWT\Types\JwtCustomerMeta;
use Tecsafe\OFCP\JWT\Types\JwtType;

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
    private const JWT_COCKPIT_JSON = "JwtCockpit.json";
    private const JWT_COCKPIT_RAW = "JwtCockpit.json.jwt";

    /**
     * Test: Parse the SalesChannel JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testCanParseSalesChannelJwt(): void
    {
        $jwtSalesChannel = JWTParser::parseSalesChannelJwt($this->getSalesChannel(true), $this->getJwks());
        $this->assertIsNumeric($jwtSalesChannel->exp);
        $this->assertIsNumeric($jwtSalesChannel->iat);
        $this->assertEquals($this->getSalesChannel()['exp'], $jwtSalesChannel->exp);
        $this->assertEquals($this->getSalesChannel()['iat'], $jwtSalesChannel->iat);
        $this->assertEquals($this->getSalesChannel()['iss'], $jwtSalesChannel->iss);
        $this->assertEquals($this->getSalesChannel()['nbf'], $jwtSalesChannel->nbf);
        $this->assertEquals($this->getSalesChannel()['sub'], $jwtSalesChannel->sub);
        $this->assertEquals($this->getSalesChannel()['type'], $jwtSalesChannel->type->value);
        $this->assertObjectHasProperty('meta', $jwtSalesChannel);
        $this->assertEquals($this->getSalesChannel(true), $jwtSalesChannel->rawToken);
    }

    /**
     * Test: Parse the Internal JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testCanParseInternalJwt(): void
    {
        $jwtInternal = JWTParser::parseInternalJwt($this->getInternal(true), $this->getJwks());
        $this->assertIsNumeric($jwtInternal->exp);
        $this->assertIsNumeric($jwtInternal->iat);
        $this->assertEquals($this->getInternal()['exp'], $jwtInternal->exp);
        $this->assertEquals($this->getInternal()['iat'], $jwtInternal->iat);
        $this->assertEquals($this->getInternal()['iss'], $jwtInternal->iss);
        $this->assertObjectHasProperty('targetServiceId', $jwtInternal->meta);
        $this->assertEquals(
            $this->getInternal()['meta']['targetServiceId'],
            $jwtInternal->meta->targetServiceId
        );
        $this->assertEquals($this->getInternal()['nbf'], $jwtInternal->nbf);
        $this->assertEquals($this->getInternal()['sub'], $jwtInternal->sub);
        $this->assertEquals($this->getInternal()['type'], $jwtInternal->type->value);
        $this->assertEquals($this->getInternal(true), $jwtInternal->rawToken);
    }

    /**
     * Test: Parse the Customer JWT
     * @return void
     * @throws ErrorRegistryException
     */
    public function testCanParseCustomerJwt(): void
    {
        $jwtCustomer = JWTParser::parseCustomerJwt($this->getCustomer(true), $this->getJwks());
        $this->assertEquals(JwtType::CUSTOMER, $jwtCustomer->type);
        $meta = $jwtCustomer->meta;

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroupId', $meta);

        $this->assertEquals($this->getCustomer()['exp'], $jwtCustomer->exp);
        $this->assertIsNumeric($jwtCustomer->exp);
        $this->assertIsNumeric($jwtCustomer->iat);
        $this->assertEquals($this->getCustomer()['iat'], $jwtCustomer->iat);
        $this->assertEquals($this->getCustomer()['iss'], $jwtCustomer->iss);
        $this->assertEquals(
            $this->getCustomer()['meta']['customerGroupId'],
            $jwtCustomer->meta->customerGroupId
        );
        $this->assertEquals(
            $this->getCustomer()['meta']['salesChannelId'],
            $jwtCustomer->meta->salesChannelId
        );
        $this->assertEquals($this->getCustomer()['nbf'], $jwtCustomer->nbf);
        $this->assertEquals($this->getCustomer()['sub'], $jwtCustomer->sub);
        $this->assertEquals($this->getCustomer()['type'], $jwtCustomer->type->value);
        $this->assertEquals($this->getCustomer(true), $jwtCustomer->rawToken);
    }

    /**
     * Test: Parse the Cockpit JWT
     */
    public function testCanParseCockpitJwt(): void
    {
        $jwtCockpit = JWTParser::parseCockpitJwt($this->getCockpit(true), $this->getJwks());
        $this->assertEquals(JwtType::COCKPIT, $jwtCockpit->type);
        $meta = $jwtCockpit->meta;

        $this->assertObjectHasProperty('role', $meta);

        $this->assertEquals($this->getCockpit()['exp'], $jwtCockpit->exp);
        $this->assertIsNumeric($jwtCockpit->exp);
        $this->assertIsNumeric($jwtCockpit->iat);
        $this->assertEquals($this->getCockpit()['iat'], $jwtCockpit->iat);
        $this->assertEquals($this->getCockpit()['iss'], $jwtCockpit->iss);
        $this->assertEquals(
            $this->getCockpit()['meta']['role'],
            $jwtCockpit->meta->role->value
        );
        $this->assertEquals($this->getCockpit()['nbf'], $jwtCockpit->nbf);
        $this->assertEquals($this->getCockpit()['sub'], $jwtCockpit->sub);
        $this->assertEquals($this->getCockpit()['type'], $jwtCockpit->type->value);
        $this->assertEquals($this->getCockpit(true), $jwtCockpit->rawToken);
    }

    public function testCanParseCustomerJwtWithoutJwks(): void
    {
        $jwtCustomer = JWTParser::parseCustomerJwt($this->getCustomer(true), null);
        $this->assertEquals(JwtType::CUSTOMER, $jwtCustomer->type);

        $meta = $jwtCustomer->meta;
        $this->assertInstanceOf(JwtCustomerMeta::class, $meta);

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroupId', $meta);
        $this->assertEquals($this->getCustomer(true), $jwtCustomer->rawToken);
    }

    /**
     * Test: Parse the Base JWT
     * @return void
     */
    public function testCanParseBaseJwt(): void
    {
        $base = JWTParser::parseBaseJwt($this->getBaseJWT(true), $this->getJwks());
        $this->assertIsNumeric($base->exp);
        $this->assertIsNumeric($base->iat);
        $this->assertEquals($this->getBaseJWT()['exp'], $base->exp);
        $this->assertEquals($this->getBaseJWT()['iat'], $base->iat);
        $this->assertEquals($this->getBaseJWT()['iss'], $base->iss);
        // Missing 'meta' in the Class, no getter available
        $this->assertEquals($this->getBaseJWT()['nbf'], $base->nbf);
        $this->assertEquals($this->getBaseJWT()['sub'], $base->sub);
        $this->assertEquals(JwtType::INTERNAL, $base->type);
        $this->assertEquals($this->getBaseJWT(true), $base->rawToken);
    }

    /**
     * Get the path to the keys (and optional including the filename)
     * @param string|null $fileName
     * @return string
     */
    public function getKeysPath(?string $fileName): string
    {
        if (!is_null($fileName)) {
            return \sprintf("%s/%s", $this->getKeysPath(null), $fileName);
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
        return \json_decode($this->getKeyContent($fileName), true);
    }

    /**
     * Get the content of a key
     * @param string|null $fileName
     * @return string
     */
    private function getKeyContent(?string $fileName): string
    {
        return \file_get_contents($this->getKeysPath($fileName));
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

    /**
     * Get the sales channel (raw or json)
     * @param bool $rawJwt
     * @return string|array
     */
    private function getCockpit(bool $rawJwt = false): string|array
    {
        if ($rawJwt) {
            return $this->getKeyContent(self::JWT_COCKPIT_RAW);
        } else {
            return $this->getKeyAndJsonDecode(self::JWT_COCKPIT_JSON);
        }
    }
}
