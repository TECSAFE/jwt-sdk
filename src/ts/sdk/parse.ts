import { JwtBase, JwtType, JwtCustomer, JwtInternal, JwtSalesChannel } from '../types/index';
import { type createLocalJWKSet } from 'jose';

type JwkType = ReturnType<typeof createLocalJWKSet>;

/**
 * Parse a JWT token, validates it and returns the payload.
 * If the token is invalid, the function will return null.
 * This function has an fail-safe mechanism, so it will never throw an error.
 * (Tho it will log a warning if the token is invalid)
 * 
 * @param token The JWT token to parse
 * @param jwk The JWK to validate the token
 */
export async function parseUnknownJwt(token: string, jwk?: JwkType): Promise<JwtBase | null> {
  try {
    if (!jwk) {
      const parts = token.split('.');
      if (parts.length !== 3) return null;
      const payload = JSON.parse(atob(parts[1]));
      if (!Object.values(JwtType).includes(payload.type)) return null;
      return payload as any as JwtBase;
    } else {
      const { jwtVerify } = await import('jose');
      const { payload } = await jwtVerify(token, jwk);
      if (!Object.values(JwtType).includes((payload as any).type)) return null;
      return payload as any as JwtBase;
    }
  } catch (e) {
    console.warn('Failed to parse and/or validate JWT token', e);
  }
  return null;
}

/**
 * Parse a JWT token and validate it as a customer token.
 * Uses internally the {@link parseUnknownJwt} function.
 */
export async function parseCustomerJwt(token: string, jwk?: JwkType): Promise<JwtCustomer | null> {
  const jwt = await parseUnknownJwt(token, jwk);
  if (!jwt || jwt.type !== JwtType.CUSTOMER) return null;
  return jwt as any as JwtCustomer;
}

/**
 * Parse a JWT token and validate it as an internal token.
 * Uses internally the {@link parseUnknownJwt} function.
 */
export async function parseInternalJwt(token: string, jwk?: JwkType): Promise<JwtInternal | null> {
  const jwt = await parseUnknownJwt(token, jwk);
  if (!jwt || jwt.type !== JwtType.INTERNAL) return null;
  return jwt as any as JwtInternal;
}

/**
 * Parse a JWT token and validate it as a sales channel token.
 * Uses internally the {@link parseUnknownJwt} function.
 */
export async function parseSalesChannelJwt(token: string, jwk?: JwkType): Promise<JwtSalesChannel | null> {
  const jwt = await parseUnknownJwt(token, jwk);
  if (!jwt || jwt.type !== JwtType.SALES_CHANNEL) return null;
  return jwt as any as JwtSalesChannel;
}
