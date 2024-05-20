import {JwtBase, JwtType} from './base.js';

/**
 * The structure of a JWT token for a customer of an authenticated sales channel
 */
export interface JwtCustomer extends JwtBase {
  /**
   * The user id of the customer
   */
  sub: string;

  /**
   * For customer tokens, the type will always be "customer"
   */
  type: JwtType.CUSTOMER;

  /**
   * @inheritdoc
   */
  meta: {
    /**
     * The sales channel id of the customer
     */
    salesChannelId: string;

    /**
     * The group of the customer inside of the sales channel
     */
    customerGroup: string;

    // TODO: add customer specific fields
  }
}
