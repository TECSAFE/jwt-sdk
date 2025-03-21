<?php

declare(strict_types=1);

namespace Tecsafe\OFCP\JWT\Test\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use Tecsafe\OFCP\JWT\Types\CockpitRole;
use PHPUnit\Framework\TestCase;

#[CoversClass(CockpitRole::class)]
class CockpitRoleTest extends TestCase
{
    public function testCanCompareRoles(): void
    {
        $this->assertTrue(CockpitRole::compareRoles(
            CockpitRole::PLATFORM_ADMIN,
            CockpitRole::COMPANY_ADMIN
        ));

        $this->assertTrue(CockpitRole::compareRoles(
            CockpitRole::COMPANY_ADMIN,
            CockpitRole::COMPANY_ADMIN
        ));

        $this->assertFalse(CockpitRole::compareRoles(
            CockpitRole::COMPANY_ADMIN,
            CockpitRole::PLATFORM_ADMIN
        ));

        $this->assertFalse(CockpitRole::compareRoles(
            CockpitRole::COMPANY_SALES_MANAGER,
            CockpitRole::PLATFORM_ADMIN,
        ));
    }

    public function testCanCheckIfHasRole(): void
    {
        $companySalesManager = CockpitRole::COMPANY_SALES_MANAGER;

        $this->assertFalse($companySalesManager->hasRole(CockpitRole::PLATFORM_ADMIN));
        $this->assertTrue($companySalesManager->hasRole(CockpitRole::COMPANY_SALES_MANAGER));


        $companyAdmin = CockpitRole::COMPANY_ADMIN;

        $this->assertFalse($companyAdmin->hasRole(CockpitRole::PLATFORM_ADMIN));
        $this->assertTrue($companyAdmin->hasRole(CockpitRole::COMPANY_ADMIN));
    }
}
