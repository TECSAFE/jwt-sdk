<?php

include "../src/php/sdk/Cache.php";
include "../src/php/sdk/Parse.php";

require_once __DIR__ . '/../vendor/autoload.php';

switch(getenv('STAGE')) {
  case 'getJWK':
    $jwk = Tecsafe\OFCP\JWT\SDK\get_jwk('http://127.0.0.1:3000/jwk.json');
    assert(is_array($jwk));
    assert(array_key_exists('keys', $jwk));
    break;

  case 'parseCustomerJwt':
    $jwk = json_decode(file_get_contents(__DIR__ . '/example/jwk.json'), true);
    $jwt = file_get_contents(__DIR__ . '/example/unknown.key');
    $content = Tecsafe\OFCP\JWT\SDK\parse_jwt_customer($jwt, $jwk);
    assert($content === null);
    $jwt = file_get_contents(__DIR__ . '/example/customer.key');
    $content = Tecsafe\OFCP\JWT\SDK\parse_jwt_customer($jwt, $jwk);
    assert($content !== null);
    break;

  case 'parseUnknownJwt':
    $jwt = file_get_contents(__DIR__ . '/example/unknown.key');
    $content = Tecsafe\OFCP\JWT\SDK\parse_jwt_base($jwt, null);
    assert($content !== null);
    break;

  default:
    echo "Invalid STAGE";
    exit(1);
}
