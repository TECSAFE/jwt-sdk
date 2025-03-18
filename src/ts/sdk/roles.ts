export const CockpitRolesEnum = {
  'PLATFORM_ADMIN': 'platform_admin',
  'COMPANY_ADMIN': 'company_admin',
  'COMPANY_ACCOUNTING': 'company_accounting',
  'COMPANY_USER': 'company_user',
}

export const CockpitRoles = Object.keys(CockpitRolesEnum);
export type CockpitRole = keyof typeof CockpitRolesEnum;

export function compareRoles(has: CockpitRole, needs: CockpitRole): boolean {
  const list = Object.keys(CockpitRolesEnum);
  return list.indexOf(has) <= list.indexOf(needs);
}
