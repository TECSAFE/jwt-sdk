import { CockpitRole } from '../sdk/roles.js';
import {JwtBase, JwtType} from './base.js';

/**
 * The structure of a JWT token for an cockpit user.
 */
export interface JwtCockpit extends JwtBase {
  /**
   * The user id of the cockpit user
   */
  sub: string;

  /**
   * For cockpit tokens, the type will always be "cockpit"
   */
  type: JwtType.COCKPIT;

  /**
   * @inheritdoc
   */
  meta: JwtCockpitMeta
}

export interface JwtCockpitMeta {
    /**
     * The role of the user
     */
    role: CockpitRole;

    /**
     * The user's email address
     */
    email: string

    /**
     * The user's first name
     */
    firstName: string

    /**
     * The user's last name
     */
    lastName: string

    /**
     * The user's organization
     */
    organization: string

    /**
     * The OIDC providers subject identifier
     */
    oidcSub: string
}