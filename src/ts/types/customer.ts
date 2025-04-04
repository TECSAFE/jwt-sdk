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
  meta: JwtCustomerMeta

}

export interface JwtCustomerMeta {

      /**
     * The sales channel id of the customer
     */
    salesChannelId: string;

    /**
     * The group id of the customer inside of the sales channel
     */
    customerGroupId: string;

    /**
     * The external group name of the customer
     */
    externalGroupName: string | null;

    /**
     * Currency of the customer
     */
    currencyId: string;

    /**
     * Currency ISO 4217 code
     */
    currencyIso: string;

    /**
     * Is the customer an guest customer?
     */
    guest: boolean;

    /**
     * The customer's email address, in most cases only available if guest is false
     */
    email: string | null;

    /**
     * A from the external sales channel provided customer id
     */
    customerIdentifier: string;

    /**
     * The sales channel access key of the headless shop
     */
    accessKey: string;

    /**
     * The sales channel context token of the headless shop
     */
    contextToken: string;

}
