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


class JWKParser
{
    /**
     * Internal base function to parse a JWT
     *
     * @param array<mixed, mixed>|null $jwk
     * @throws \Exception
     */
    public static function parseJwk(string $jwt, ?array $jwk): ?array
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
    public static function parseBaseJwk(string $jwt, ?array $jwk): ?JwtBase
    {
        $content = self::parseJwk($jwt, $jwk);

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
    public static function parseCustomerJwk(string $jwt, ?array $jwk): ?JwtCustomer
    {
        $content = self::parseJwk($jwt, $jwk);

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
    public static function parseInternalJwk(string $jwt, ?array $jwk): ?JwtInternal
    {
        $content = self::parseJwk($jwt, $jwk);

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
    public static function parseSalesChannelJwk(string $jwt, ?array $jwk): ?JwtSalesChannel
    {
        $content = self::parseJwk($jwt, $jwk);

        if ($content === null) {
            return null;
        }

        return new JwtSalesChannel($content);
    }
}
