<?php
/**
 * Class WC_Payment_Token_eCheck file.
 *
 * @package ClassicCommerce\PaymentTokens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Classic Commerce eCheck Payment Token.
 *
 * Representation of a payment token for eChecks.
 *
 * @class       WC_Payment_Token_ECheck
 * @version     WC-3.0.0
 * @since       WC-2.6.0
 * @package     ClassicCommerce/PaymentTokens
 */
class WC_Payment_Token_ECheck extends WC_Payment_Token {

	/**
	 * Token Type String.
	 *
	 * @var string
	 */
	protected $type = 'eCheck';

	/**
	 * Stores eCheck payment token data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'last4' => '',
	);

	/**
	 * Get type to display to user.
	 *
	 * @since  WC-2.6.0
	 * @param  string $deprecated Deprecated since WooCommerce 3.0.
	 * @return string
	 */
	public function get_display_name( $deprecated = '' ) {
		$display = sprintf(
			/* translators: 1: credit card type 2: last 4 digits 3: expiry month 4: expiry year */
			__( 'eCheck ending in %1$s', 'classic-commerce' ),
			$this->get_last4()
		);
		return $display;
	}

	/**
	 * Hook prefix
	 *
	 * @since WC-3.0.0
	 */
	protected function get_hook_prefix() {
		return 'woocommerce_payment_token_echeck_get_';
	}

	/**
	 * Validate eCheck payment tokens.
	 *
	 * These fields are required by all eCheck payment tokens:
	 * last4  - string Last 4 digits of the check
	 *
	 * @since WC-2.6.0
	 * @return boolean True if the passed data is valid
	 */
	public function validate() {
		if ( false === parent::validate() ) {
			return false;
		}

		if ( ! $this->get_last4( 'edit' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the last four digits.
	 *
	 * @since  WC-2.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string Last 4 digits
	 */
	public function get_last4( $context = 'view' ) {
		return $this->get_prop( 'last4', $context );
	}

	/**
	 * Set the last four digits.
	 *
	 * @since WC-2.6.0
	 * @param string $last4 eCheck last four digits.
	 */
	public function set_last4( $last4 ) {
		$this->set_prop( 'last4', $last4 );
	}
}
