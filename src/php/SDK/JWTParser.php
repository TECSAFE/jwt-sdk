<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\SDK;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Tecsafe\OFCP\JWT\Types\JwtBase;
use Tecsafe\OFCP\JWT\Types\JwtCustomer;
use Tecsafe\OFCP\JWT\Types\JwtInternal;
use Tecsafe\OFCP\JWT\Types\JwtSalesChannel;
use Tecsafe\OFCP\JWT\Types\JwtCockpit;

class JWTParser
{
    /**
     * Internal base function to parse a JWT
     */
    public static function parseJwt(string $jwt, ?array $jwk): ?array
    {
        if ($jwk !== null) {
            $keys = JWK::parseKeySet($jwk);
            $decoded = JWT::decode($jwt, $keys);

            $content =\json_decode(\json_encode($decoded), true);
        } else {
            $parts = \explode(".", $jwt);

            if (count($parts) !== 3) {
                throw new \Exception("Invalid JWT format");
            }

            $content = \json_decode(base64_decode($parts[1]), true);
        }

        return $content;
    }

    /**
     * Parse a JWT and return a JwtBase object
     */
    public static function parseBaseJwt(string $jwt, ?array $jwk): ?JwtBase
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return JwtBase::fromJson($content, $jwt);
    }

    /**
     * Parse a JWT and return a JwtCustomer object
     */
    public static function parseCustomerJwt(string $jwt, ?array $jwk): ?JwtCustomer
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return JwtCustomer::fromJson($content, $jwt);
    }

    /**
     * Parse a JWT and return a JwtInternal object
     */
    public static function parseInternalJwt(string $jwt, ?array $jwk): ?JwtInternal
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return JwtInternal::fromJson($content, $jwt);
    }

    /**
     * Parse a JWT and return a JwtSalesChannel object
     */
    public static function parseSalesChannelJwt(string $jwt, ?array $jwk): ?JwtSalesChannel
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return JwtSalesChannel::fromJson($content, $jwt);
    }

    /**
     * Parse a JWT and return a JwtCockpit object
     */
    public static function parseCockpitJwt(string $jwt, ?array $jwk): ?JwtCockpit
    {
        $content = self::parseJwt($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return JwtCockpit::fromJson($content, $jwt);
    }
}
