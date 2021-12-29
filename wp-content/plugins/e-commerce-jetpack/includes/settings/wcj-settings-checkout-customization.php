<?php
/**
 * Booster for WooCommerce - Settings - Checkout Customization
 *
 * @version 4.6.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

return array(
	array(
		'title'    => __( 'Restrict Countries by Customer\'s IP', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_restrict_countries_options',
	),
	array(
		'title'    => __( 'Restrict Billing Countries by Customer\'s IP', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_restrict_countries_by_customer_ip_billing',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Restrict Shipping Countries by Customer\'s IP', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'To restrict shipping countries, "Shipping location(s)" option in %s must be set to "Ship to specific countries only" (and you can leave "Ship to specific countries" option empty there).', 'e-commerce-jetpack' ),
			'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=general' ) . '">' .
				__( 'WooCommerce > Settings > General', 'e-commerce-jetpack' ) . '</a>' ) . '<br>' . apply_filters( 'booster_message', '', 'desc' ),
		'id'       => 'wcj_checkout_restrict_countries_by_customer_ip_shipping',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Ignore on Admin', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Ignores restriction on admin', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_restrict_countries_by_customer_ip_ignore_admin',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Restrict By Customer\'s Billing Country', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Restricts based on Customer\'s Billing Country, ignoring other restrictions', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_restrict_countries_by_user_billing_country',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Restrict based on a YITH manual order', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Enable if you are creating a manual order using "YITH WooCommerce Request a Quote" plugin and selecting the billing country manually', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_restrict_countries_based_on_yith_raq',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Conditions', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'The restriction will work only if some condition is true.', 'e-commerce-jetpack' ).'<br /> '.__( 'Leave it empty if you want to restrict countries everywhere.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_restrict_countries_by_customer_ip_conditions',
		'default'  => 'no',
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array(
			'is_cart'     => __( 'Is Cart', 'popup-notices-for-woocommerce' ),
			'is_checkout' => __( 'Is Checkout', 'popup-notices-for-woocommerce' ),
		)
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_restrict_countries_options',
	),
	array(
		'title'    => __( '"Create an account?" Checkbox Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_create_account_checkbox_options',
	),
	array(
		'title'    => __( '"Create an account?" Checkbox', 'e-commerce-jetpack' ),
		'desc_tip' => __( '"Create an account?" checkbox default value', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_create_account_default_checked',
		'default'  => 'default',
		'type'     => 'select',
		'options'  => array(
			'default'     => __( 'WooCommerce default', 'e-commerce-jetpack' ),
			'checked'     => __( 'Checked', 'e-commerce-jetpack' ),
			'not_checked' => __( 'Not checked', 'e-commerce-jetpack' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_create_account_checkbox_options',
	),
	array(
		'title'    => __( '"Order Again" Button Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_order_again_button_options',
	),
	array(
		'title'    => __( 'Hide "Order Again" Button on "View Order" Page', 'e-commerce-jetpack' ),
		'desc'     => __( 'Hide', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_hide_order_again',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_order_again_button_options',
	),
	array(
		'title'    => __( 'Disable Fields on Checkout for Logged Users', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged_options',
	),
	array(
		'title'    => __( 'Fields to Disable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged',
		'default'  => array(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array(
			'billing_country'     => __( 'Billing country', 'e-commerce-jetpack' ),
			'billing_first_name'  => __( 'Billing first name', 'e-commerce-jetpack' ),
			'billing_last_name'   => __( 'Billing last name', 'e-commerce-jetpack' ),
			'billing_company'     => __( 'Billing company', 'e-commerce-jetpack' ),
			'billing_address_1'   => __( 'Billing address 1', 'e-commerce-jetpack' ),
			'billing_address_2'   => __( 'Billing address 2', 'e-commerce-jetpack' ),
			'billing_city'        => __( 'Billing city', 'e-commerce-jetpack' ),
			'billing_state'       => __( 'Billing state', 'e-commerce-jetpack' ),
			'billing_postcode'    => __( 'Billing postcode', 'e-commerce-jetpack' ),
			'billing_email'       => __( 'Billing email', 'e-commerce-jetpack' ),
			'billing_phone'       => __( 'Billing phone', 'e-commerce-jetpack' ),
			'shipping_country'    => __( 'Shipping country', 'e-commerce-jetpack' ),
			'shipping_first_name' => __( 'Shipping first name', 'e-commerce-jetpack' ),
			'shipping_last_name'  => __( 'Shipping last name', 'e-commerce-jetpack' ),
			'shipping_company'    => __( 'Shipping company', 'e-commerce-jetpack' ),
			'shipping_address_1'  => __( 'Shipping address 1', 'e-commerce-jetpack' ),
			'shipping_address_2'  => __( 'Shipping address 2', 'e-commerce-jetpack' ),
			'shipping_city'       => __( 'Shipping city', 'e-commerce-jetpack' ),
			'shipping_state'      => __( 'Shipping state', 'e-commerce-jetpack' ),
			'shipping_postcode'   => __( 'Shipping postcode', 'e-commerce-jetpack' ),
			'order_comments'      => __( 'Order comments', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Message for Logged Users', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'You can use HTML here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged_message',
		'default'  => '<em>' . __( 'This field can not be changed', 'e-commerce-jetpack' ) . '</em>',
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Advanced: Custom Fields (Readonly)', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => sprintf( __( 'Comma separated list of fields ids, e.g.: %s.', 'e-commerce-jetpack' ), '<em>billing_wcj_checkout_field_1, billing_wcj_checkout_field_2</em>' ),
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged_custom_r',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Advanced: Custom Fields (Disabled)', 'e-commerce-jetpack' ),
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'desc_tip' => sprintf( __( 'Comma separated list of fields ids, e.g.: %s.', 'e-commerce-jetpack' ), '<em>billing_wcj_checkout_field_1, billing_wcj_checkout_field_2</em>' ),
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged_custom_d',
		'default'  => '',
		'type'     => 'text',
		'css'      => 'width:100%;',
		'custom_attributes' => apply_filters( 'booster_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_customization_disable_fields_for_logged_options',
	),
	array(
		'title'    => __( '"Order received" Message Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_customization_order_received_message_options',
	),
	array(
		'title'    => __( 'Customize Message', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_customization_order_received_message_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc_tip' => __( 'You can use HTML and/or shortcodes here.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_customization_order_received_message',
		'default'  => __( 'Thank you. Your order has been received.', 'woocommerce' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_customization_order_received_message_options',
	),
	array(
		'title'    => __( '"Returning customer?" Message Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_customization_checkout_login_message_options',
	),
	array(
		'title'    => __( 'Customize Message', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_customization_checkout_login_message_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'wcj_checkout_customization_checkout_login_message',
		'default'  => __( 'Returning customer?', 'woocommerce' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_customization_checkout_login_message_options',
	),
	array(
		'title'    => __( 'Recalculate Checkout', 'e-commerce-jetpack' ),
		'desc'     => __( 'Recalculate checkout right after the default calculation has been requested.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_recalculate_checkout_update_options',
	),
	array(
		'title'    => __( 'Recalculate Checkout', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_recalculate_checkout_update_enable',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Fields', 'e-commerce-jetpack' ),
		'desc'     => __( 'Required fields that need to be changed in order to recalculate checkout.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Use CSS selector syntax.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_recalculate_checkout_update_fields',
		'default'  => '#billing_country, #shipping_country',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_recalculate_checkout_update_options',
	),
	array(
		'title'    => __( 'Force Checkout Update', 'e-commerce-jetpack' ),
		'desc'     => __( 'Update checkout when some field have its value changed.', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_force_checkout_update_options',
	),
	array(
		'title'    => __( 'Force Checkout Update', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_force_checkout_update_enable',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Fields', 'e-commerce-jetpack' ),
		'desc'     => __( 'Fields that need to be changed in order to update checkout.', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Use CSS selector syntax.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_force_checkout_update_fields',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_force_checkout_update_options',
	),
);