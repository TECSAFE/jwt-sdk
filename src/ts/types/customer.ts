import {JwtBase, JwtType} from './base';

/**
 * The meta object for a customer token
 */
export interface JwtCustomerMeta {
  /**
   * The sales channel id of the customer
   */
  salesChannelId: string;

  /**
   * The sales channel access key
   */
  accessKey: string;

  /**
   * The group of the customer inside of the sales channel
   */
  customerGroup: string;
}

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
  meta: JwtCustomerMeta;
}
