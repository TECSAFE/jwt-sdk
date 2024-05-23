import {JwtBase, JwtType} from './base';

/**
 * The meta object for an internal token
 */
export interface JwtInternalMeta {
  /**
   * The service names, for which this token can be use.
   * 
   */
  targetServiceId: string[];
}

/**
 * The structure of a JWT token for an internal micro service.
 * A token of this type will only be used for internal communication between micro services.
 */
export interface JwtInternal extends JwtBase {
  /**
   * The internal micro service id, which requested the token
   */
  sub: string;

  /**
   * For internal tokens, the type will always be "internal"
   */
  type: JwtType.INTERNAL;

  /**
   * @inheritdoc
   */
  meta: JwtInternalMeta;
}
