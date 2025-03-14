<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\SDK;

use PHPUnit\Framework\Attributes\CoversClass;
use Tecsafe\OFCP\JWT\SDK\JWTParser;
use PHPUnit\Framework\TestCase;

#[CoversClass(JWTParser::class)]
class JWTParserTest extends TestCase
{
    private const CUSTOMER_JWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IjJhNWEyYWZkLTZhNDktNDg5MC1hOGM5LTUxOTQwNWZjMjk1YyJ9.eyJ0eXBlIjoiY3VzdG9tZXIiLCJleHAiOjE3ODI2NTQ3NzcsImlhdCI6MTcxOTQwOTk3NywibmJmIjoxNzE5NDA5OTc3LCJpc3MiOiJhcGktZ2F0ZXdheSIsInN1YiI6IjEyMzQ1Njc4OTAiLCJtZXRhIjp7InNhbGVzQ2hhbm5lbElkIjoiZm9vYmFyIiwiY3VzdG9tZXJHcm91cCI6ImZvb2JheiJ9fQ.dU8jgtpnn5SN5Ojj537mL-5EWZ9otTOal9nnhJ3xRuDVTY9O7NX19X1EL-zzcetlewNFxJrX3L9nhHTPsNy2B26FujMkjmNKZXoCMxqR23-Np0isdNfDlPQ53i2CshPlF3UPwEQa-8Co-6uhF0qc77G5Pykgj3OEXFojqzr1rOexpz9SkaxHy2CWj2Q5c8x0lUug-rDhanfGpNaF-ijNrYiPpmQBmqvoxQYZDZDD1ozFPZnEL_mCQ6CkrAnz9_S_dtIyjGhJCcf9SdSXvcBttMcTs-KXEmCEL3n-YERBrRVwB01ItccxNqTA6yFfc5TDGHvnmiPrsJhTJh6hMCSpKQ';
    private const UNKNOWN_JWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IjJhNWEyYWZkLTZhNDktNDg5MC1hOGM5LTUxOTQwNWZjMjk1YyJ9.eyJ0eXBlIjoidW5rbm93biIsImV4cCI6MTc4MjY1NDc3NywiaWF0IjoxNzE5NDA5OTc3LCJuYmYiOjE3MTk0MDk5NzcsImlzcyI6ImFwaS1nYXRld2F5Iiwic3ViIjoiMTIzNDU2Nzg5MCIsIm1ldGEiOltdfQ.qnatDh54LKmOd39zgxc6az7FGF9-iI8qGHzhC5jV3P9YbMTy3aFU-1_oUYTvja-z0YnglrFECI2hgZ2EN2WDiwkvJB3li-Gi4bVWVqKQ8URilzzjTH2nX1ybEaeEqDyza2TpwHT23MnX4ppi8FgaQ9wYPQyyMK8Gf4PmCiH2dcSlUiEsKyqPkRLhYeRhxOWlHL7Bhac9XrhKcTCeDoxH3C1NMiZrImbGKPXIJRkcBgbgVZGDgQtUD37x_7nK5fltiZ2yLr6zZqwFotnJIjwxsZYRvpfI3fV76nZSJjH6nEHUD9XpodZgTdATlTBbP-C8NYRht_oT_QV1wlwPmL-Ylg';

    /*public function testParseSalesChannelJwk()
    {
    }

    public function testParseJwk()
    {
    }

    public function testParseInternalJwk()
    {
    }*/

    public function testParseBaseJwk()
    {
        $this->expectException(\PHPModelGenerator\Exception\ErrorRegistryException::class);
        $this->expectExceptionMessage('Invalid value for type declined by enum constraint');

        JWTParser::parseBaseJwt(self::UNKNOWN_JWT, $this->getJWKS());
    }

    public function testParseCustomerJwt()
    {
        $jwtCustomer = JWTParser::parseCustomerJwt(self::CUSTOMER_JWT, $this->getJWKS());
        $this->assertEquals('customer', $jwtCustomer->getType());

        $meta = $jwtCustomer->getMeta();

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroup', $meta);
        $this->assertTrue(\method_exists($meta, 'getSalesChannelId'), 'Class does not have method getSalesChannelId');
        $this->assertTrue(\method_exists($meta, 'getCustomerGroup'), 'Class does not have method getCustomerGroup');

        $this->assertEquals('api-gateway', $jwtCustomer->getIss());
    }

    public function testParseCustomerJwtWithoutJwks()
    {
        $jwtCustomer = JWTParser::parseCustomerJwt(self::CUSTOMER_JWT, null);
        $this->assertEquals('customer', $jwtCustomer->getType());

        $meta = $jwtCustomer->getMeta();

        $this->assertObjectHasProperty('salesChannelId', $meta);
        $this->assertObjectHasProperty('customerGroup', $meta);
        $this->assertTrue(\method_exists($meta, 'getSalesChannelId'), 'Class does not have method getSalesChannelId');
        $this->assertTrue(\method_exists($meta, 'getCustomerGroup'), 'Class does not have method getCustomerGroup');

        $this->assertEquals('api-gateway', $jwtCustomer->getIss());
    }

    private function getJWKS(): array
    {
        return \json_decode(\file_get_contents(__DIR__ . '/../Resources/jwks.json'), true);
    }
}
