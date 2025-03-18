export enum JwtType {
  /**
   * Token for an customer of an authenticated sales channel
   */
  CUSTOMER = 'customer',

  /**
   * Token for registered sales channel
   */
  SALES_CHANNEL = 'sales-channel',

  /**
   * Token for a cockpit user
   */
  COCKPIT = 'cockpit',

  /**
   * Token for an internal user
   */
  INTERNAL = 'internal',
}

/**
 * The base structure of a JWT token
 */
export interface JwtBase {
  /**
   * The subject of the token, user id, sales channel id, micro service id, etc.
   */
  sub: string;

  /**
   * The JWT ID, a unique identifier for the token
   */
  jti: string;

  /**
   * Unix timestamp of when the token was issued
   */
  iat: number;

  /**
   * Unix timestamp of when the token expires
   */
  exp: number;

  /**
   * Unix timestamp of when the token becomes active
   */
  nbf: number;

  /**
   * The issuer of the token, usually will equal to "api-gateway"
   */
  iss: string;

  /**
   * The token's type,
   */
  type: JwtType;

  /**
   * The meta object contains additional information about the token, or the token's owner
   */
  meta: {
    [key: string]: any;
  };
}
