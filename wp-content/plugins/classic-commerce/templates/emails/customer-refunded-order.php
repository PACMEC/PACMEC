<?php
/**
 * Customer refunded order email
 *
 * This template can be overridden by copying it to yourtheme/classic-commerce/emails/customer-refunded-order.php.
 *
 * @see     https://classiccommerce.cc/docs/installation-and-setup/template-structure/
 * @package ClassicCommerce/Templates/Emails
 * @version WC-3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( __( 'Hi %s,', 'classic-commerce' ), $order->get_billing_first_name() ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

<p>
<?php
if ( $partial_refund ) {
	/* translators: %s: Site title */
	printf( __( 'Your order on %s has been partially refunded. There are more details below for your reference:', 'classic-commerce' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
} else {
	/* translators: %s: Site title */
	printf( __( 'Your order on %s has been refunded. There are more details below for your reference:', 'classic-commerce' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
}
?>
</p>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since WC-2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

?>
<p>
<?php _e( 'We hope to see you again soon.', 'classic-commerce' ); // phpcs:ignore WordPress.XSS.EscapeOutput ?>
</p>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );