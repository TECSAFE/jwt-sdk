<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

$payload = \json_decode(<<<JSON
{
    "type": "customer",
    "exp": 1782654777,
    "iat": 1719409977,
    "nbf": 1719409977,
    "iss": "api-gateway",
    "sub": "1234567890",
    "meta": {
        "salesChannelId": "foobar",
        "customerGroup": "foobaz"
    }
}
JSON, true);

$header = \json_decode(<<<JSON
{
"typ": "JWT"
}
JSON, true);


$keyId = '2a5a2afd-6a49-4890-a8c9-519405fc295c';
$alg = 'RS256';

$jwks = \json_decode(<<<JSON
{
  "keys": [
    {
      "alg": "RS256",
      "e": "AQAB",
      "key_ops": [
        "verify"
      ],
      "kty": "RSA",
      "n": "sdiB7mocsTw9t3oXwxBUpTPEuazEmx3ORccv1M3ZzUxKOi8t9Zz5fTh0nRIKtGvcT4AiJuL7CmTMKUNhSscHr-1w1HDBTmfkxUx3oOSTOLUCyuyx50cWrrwTKgS5q1Ha44GBgUO1K3jHb-IinhIeYf7bdtNgtt7jtPj26ipAw4acQHyMENPqqF1z2IbXCYLtoqWlBrTIPiveJP4ruV4E6VlcetrDDNu5y50F00z58q9lKM97SF3f6k0INwT90_YEsJSTMBLGIwTwxhw0mpwlqXvDnDZyQ4sVuTWKhpXz2pb5Tf1cA6zM9hrIGXD0yDNny1y7V-L08F2OP11uEf4lmQ",
      "use": "sig",
      "kid": "e69a771496b22a7b46f0d8d7e5dbed4f"
    },
    {
      "kty": "RSA",
      "use": "sig",
      "key_ops": [
        "sign",
        "verify"
      ],
      "alg": "RS256",
      "kid": "2a5a2afd-6a49-4890-a8c9-519405fc295c",
      "n": "6klnqw9HQIwvwCARuGavQJEQirk2gIaKULvL1PIVPqgu4yhEdrLDHUxghKTkTsl7Tl3AvVZCn3GzQRNujv4nY5QQo289gOryNgzX5swKT2e56EBW4jFAzoBm0bn81Y7ur5nO4_SndXXVTBhTz8A03wdSwuulY2_Z_BdNx-DPOimfIUcJws4jxyvAQEzz3p80flKjZrjLP_CLXbx0qq0quX1meXALJjg844b02owL-_lvf2B9NlOulrks1iCThw9jBz6ff9SV0sVlVI9UoHgOUMnggJx4s82YSb8E2m93r8D5DIborkE9L0TzycQo1GS_FcvS2SMfv209dqwlD9ngyQ",
      "e": "AQAB",
      "p": "9j-Q7eDiQFpkyzdODWMxSG4v87rmLrazwG53FIml_6nmxxWfZCTY4c9hjx4SXoaAMYfbC_uzBcmqbuubYe9P29xCb_7xLHNDIdIJbRAt7A-wCVgd3OjCKgqPxXzC9lUEjGVpZT6dJB3WDGfSTmV3L6BqUBbYLH6sinFPtBaUWOU",
      "q": "85CS8KcgN47PfM1ax31nf63_BkevHYzTCgfIvqPE0VZPmj6OC74W28uVSokXQtrMNaq-mS1Km3B4DFbspGqZODJpPvqeE3HB_J7ubAYX9pbyn9BiTFAUHq-IRSFfwVtH7mCGV_Bf1mJobgl0AtJeTIwPwPY8M7MjNtttsTy43hU",
      "dp": "aWjwi0OW1mFbgDoeaVCqygyQ7k51Nk3zSE6BHwOFUZnTNimlX2L-jDsP0gnXZytAOPOk6s5F5hZbpLuqehPNJZp1vidt6c81T4bvjgY9Ai389yMHNYdVhlWbEbjEZFokZk0K_tH8xncXJ5xRxEle6b5LhBkjVHitN14l9jssKZk",
      "dq": "NwWg_DZ1A7gCTWpCqLA-skxHQU7uU1mdzzVE9zQd4UCDSQ_6nNJ7bXnHJwhjNdohqboBxq7BnO3CYN8-JEJkjJpuxihtj2zyarQonkSeIzmkY8_6dMzeTrROr3dDn3WUgVhS4QjPTan7d2gxSfXmEZrOcEPwA6njOaBGXnSeOqU",
      "qi": "LrIwwMN4DOgjx7VrY7ppQ6V0ay-aPAL9KrZR65hhBPxcPuc7c0uFQWNTHeAfDVdTuzT0y4c9ChiZffcGAZZlZt3kIsPmPoeCV1MIh4fUYOi9YVnCIBs5eUDHkIL-iIeZIiiVyDpKdmNnjaPnK07PRqrxRYXf9tpLoQMCwHJabrI"
    }
  ]
}

JSON, true);

#$keys = \Firebase\JWT\JWK::parseKeySet($keys);


#/* @var $key \Firebase\JWT\Key */
#$key = \array_pop($keys);


$key = \openssl_pkey_get_private(\file_get_contents(__DIR__ . '/Resources/private-key.pem')); #\OpenSSLAsymmetricKey::class

$jwt = \Firebase\JWT\JWT::encode($payload, $key, $alg, $keyId, $header);

echo $jwt . "\r\n";

