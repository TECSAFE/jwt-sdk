<?php

namespace Tecsafe\OFCP\JWT\SDK;

function get_jwk(
  $url = "https://api-gateway.tecsafe.example.com/.well-known/jwks",
  $ttl = 15 * 60,
) {
  $CACHE_FILE = __DIR__ ."/jwk-cache.json";

  $now = time();

  if (file_exists($CACHE_FILE)) {
    $content = json_decode(file_get_contents($CACHE_FILE), true);
    if ($content['expires'] > $now) return $content['jwk'];
  }

  $jwk = json_decode(file_get_contents($url), true);
  file_put_contents($CACHE_FILE, json_encode([
    'jwk' => $jwk,
    'expires' => $now + $ttl,
  ]));
  return $jwk;
}
