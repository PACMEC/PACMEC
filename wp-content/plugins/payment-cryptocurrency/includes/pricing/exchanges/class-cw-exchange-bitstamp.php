<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Bitstamp Exchange Rates Class
 *
 * @category   CryptoPay
 * @package    Exchange
 * @subpackage ExchangeBase
 * Author: CryptoPay AS
 * Author URI: https://cryptopay.com
 */
class CW_Exchange_Bitstamp extends CW_Exchange_Base {


	/**
	 *
	 * Get the exchange API URL
	 *
	 * @return string
	 */
	protected function get_exchange_url_format() : string {
		return 'https://www.bitstamp.net/api/v2/ticker/%s';
	}

	/**
	 *
	 * Get the exchange price index (last index)
	 *
	 * @return string
	 */
	protected function get_exchange_price_index() : string {
		return 'last';
	}

	/**
	 *
	 * Get the formatting of currency pair for exchange API
	 *
	 * @return string
	 */
	protected function get_pair_format() : string {
		return '%2$s%1$s';
	}
}
