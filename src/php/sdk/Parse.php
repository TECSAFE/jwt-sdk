<?php

namespace Tecsafe\OFCP\JWT\SDK;

use Tecsafe\OFCP\JWT\Types\JwtBase;
use Tecsafe\OFCP\JWT\Types\JwtCustomer;
use Tecsafe\OFCP\JWT\Types\JwtInternal;
use Tecsafe\OFCP\JWT\Types\JwtSalesChannel;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

/**
 * Internal base function to parse a JWT
 * @param string $jwt
 * @param array|null $jwk
 * @return array|null
 */
function parse_jwt($jwt, $jwk) {
  try {
    $content = [];
    if ($jwk !== null) {
      $jwk = JWK::parseKey($jwk);
      $content = JWT::decode($jwt, $jwk);
    } else {
      $parts = explode(".", $jwt);
      if (count($parts) !== 2) throw new \Exception("Invalid JWT format");
      $content = json_decode(base64_decode($parts[1]), true);
    }
    return $content;
  } catch (\Exception $ignored) {
    return null;
  }
}

/**
 * Parse a JWT and return a JwtBase object
 * @param string $jwt
 * @param array|null $jwk
 * @return JwtBase|null
 */
function parse_jwt_base($jwt, $jwk) {
  $content = parse_jwt($jwt, $jwk);
  if ($content === null) return null;
  return new JwtBase($content);
}

/**
 * Parse a JWT and return a JwtCustomer object
 * @param string $jwt
 * @param array|null $jwk
 * @return JwtCustomer|null
 */
function parse_jwt_customer($jwt, $jwk) {
  $content = parse_jwt($jwt, $jwk);
  if ($content === null) return null;
  return new JwtCustomer($content);
}

/**
 * Parse a JWT and return a JwtInternal object
 * @param string $jwt
 * @param array|null $jwk
 * @return JwtInternal|null
 */
function parse_jwt_internal($jwt, $jwk) {
  $content = parse_jwt($jwt, $jwk);
  if ($content === null) return null;
  return new JwtInternal($content);
}

/**
 * Parse a JWT and return a JwtSalesChannel object
 * @param string $jwt
 * @param array|null $jwk
 * @return JwtSalesChannel|null
 */
function parse_jwt_sales_channel($jwt, $jwk) {
  $content = parse_jwt($jwt, $jwk);
  if ($content === null) return null;
  return new JwtSalesChannel($content);
}
