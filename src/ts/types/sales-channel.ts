import {JwtBase, JwtType} from './base.js';

/**
 * The structure of a JWT token for a registered sales channel
 * Sales channels can request customer tokens with this token
 */
export interface JwtSalesChannel extends JwtBase {
  /**
   * The sales channel id
   */
  sub: string;

  /**
   * For sales-channel tokens, the type will always be "sales-channel"
   */
  type: JwtType.SALES_CHANNEL;

  /**
   * @inheritdoc
   */
  meta: JwtSalesChannelMeta
}

export interface JwtSalesChannelMeta {
    /**
     * The sales channel access key of the headless shop
     */
    accessKey: string;
}