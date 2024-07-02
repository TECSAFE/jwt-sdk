# JWT SDK

This repo contains the JWT SDK for parsing and validating JWT tokens from OFCP. The main code is
written in TypeScript, but we also provide a JsonSchema and a PHP equivalent.

## Installation

**TypeScript** / **JavaScript**:

```sh
npm install @tecsafe/jwt-sdk
```

**PHP**:

```sh
composer require tecsafe/jwt-sdk
```

**JsonSchema**:

```sh
curl -O https://tecsafe.github.io/jwt-sdk/json-schema/latest.json
```

## Usage

**TypeScript** / **JavaScript**:

```typescript
import { getJWK, parseUnknownJwt } from '@tecsafe/jwt-sdk';

const TOKEN = 'eyJhbGci...';

const jwk = await getJWK();
const body = parseUnknownJwt(TOKEN, jwk);
// or if you don't want to validate the signature, and just want to parse the token
const body = parseUnknownJwt(TOKEN);
```

Visit https://tecsafe.github.io/jwt-sdk/ for a more detailed documentation.

**PHP**:

First of all, you need an implementation for 
 - [psr/http-factory](https://packagist.org/providers/psr/http-factory-implementation)
 - [psr/simple-cache](https://packagist.org/providers/psr/simple-cache-implementation)
 - [psr/http-client](https://packagist.org/providers/psr/http-client-implementation)


Example:

```shell
composer require nyholm/psr7 symfony/http-client symfony/cache
```

```php
<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use Tecsafe\OFCP\JWT\SDK\JWKLoader;
use Tecsafe\OFCP\JWT\SDK\JWTParser;

/* Load JWKS from URL */
$jwkUri = "https://api-gateway.tecsafe.example.com/.well-known/jwks";

$jwkLoader = new JWKLoader(new Psr18Client(), new Psr17Factory());
$jwk = $jwkLoader->getJWK($jwkUri);


/* Optional: Decorate JWKLoader with Cache */
use Tecsafe\OFCP\JWT\SDK\CachedJWKLoader;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

$cachedLoader = new CachedJWKLoader($jwkLoader, new Psr16Cache(new ArrayAdapter()));
$jwk = $cachedLoader->getJWK($jwkUri);
$jwk = $cachedLoader->getJWK($jwkUri); // Loaded from cache


/* Parse and validate tokens */
$TOKEN = 'eyJhbGci...';

$body = JWTParser::parseCustomerJwt($TOKEN, $jwk);
// same as above, if you don't want to validate the signature
$body = JWTParser::parseCustomerJwt($TOKEN);
```

**JsonSchema**:

See https://json-schema.org/ for more information on how to use JsonSchema.
