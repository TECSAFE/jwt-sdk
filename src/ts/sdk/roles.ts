export const CockpitRolesEnum = {
  'PLATFORM_ADMIN': 'platform_admin',
  'COMPANY_ADMIN': 'company_admin',
  'COMPANY_ACCOUNTING_MANAGER': 'company_accounting_manager',
  'COMPANY_PURCHASE_MANAGER': 'company_purchase_manager',
  'COMPANY_SALES_MANAGER': 'company_sales_manager',
}

export const CockpitRoles = Object.keys(CockpitRolesEnum);
export type CockpitRole = keyof typeof CockpitRolesEnum;

export function compareRoles(has: CockpitRole, needs: CockpitRole): boolean {
  const list = Object.keys(CockpitRolesEnum);
  return list.indexOf(has) <= list.indexOf(needs);
}
