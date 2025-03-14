<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use PHPModelGenerator\Exception\ErrorRegistryException;
use Tecsafe\OFCP\JWT\Types\JwtBase;
use Tecsafe\OFCP\JWT\Types\JwtCustomer;
use Tecsafe\OFCP\JWT\Types\JwtInternal;
use Tecsafe\OFCP\JWT\Types\JwtSalesChannel;
use Tecsafe\OFCP\JWT\Types\JwtCockpit;

class JWTParser
{
    /**
     * Internal base function to parse a JWT
     *
     * @param array<mixed, mixed>|null $jwk
     * @throws \Exception
     */
    public static function parseJwt(string $jwt, ?array $jwk): ?array
    {
        if ($jwk !== null) {
            $keys = JWK::parseKeySet($jwk);
            $decoded = JWT::decode($jwt, $keys);

            $content =\json_decode(\json_encode($decoded), true);
        } else {
            $parts = \explode(".", $jwt);

            if (count($parts) !== 2) {
                throw new \Exception("Invalid JWT format");
            }

            $content = \json_decode(base64_decode($parts[1]), true);
        }

        return $content;
    }

    /**
     * Parse a JWT and return a JwtBase object
     *
     * @param array<mixed>|null $jwk
     *
     * @throws ErrorRegistryException
     */
    public static function parseBaseJwt(string $jwt, ?array $jwk): ?JwtBase
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtBase($content);
    }

    /**
     * Parse a JWT and return a JwtCustomer object
     *
     * @param array<mixed>|null $jwk
     *
     * @throws ErrorRegistryException
     */
    public static function parseCustomerJwt(string $jwt, ?array $jwk): ?JwtCustomer
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtCustomer($content);
    }

    /**
     * Parse a JWT and return a JwtInternal object
     *
     * @param array<mixed>|null $jwk
     *
     * @throws ErrorRegistryException
     */
    public static function parseInternalJwt(string $jwt, ?array $jwk): ?JwtInternal
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtInternal($content);
    }

    /**
     * Parse a JWT and return a JwtSalesChannel object
     *
     * @param array<mixed>|null $jwk
     *
     * @throws ErrorRegistryException
     */
    public static function parseSalesChannelJwt(string $jwt, ?array $jwk): ?JwtSalesChannel
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtSalesChannel($content);
    }

    /**
     * Parse a JWT and return a JwtCockpit object
     * 
     * @param array<mixed>|null $jwk
     * 
     * @throws ErrorRegistryException
     */
    public static function parseCockpitJwt(string $jwt, ?array $jwk): ?JwtCockpit
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtCockpit($content);
    }
}
