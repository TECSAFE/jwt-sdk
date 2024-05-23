import {JwtBase, JwtType} from './base';

/**
 * The meta object for a sales channel token
 */
export interface JwtSalesChannelMeta {
  /**
   * The sales channel access key
   */
  accessKey: string;
}

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
  meta: JwtSalesChannelMeta;
}
