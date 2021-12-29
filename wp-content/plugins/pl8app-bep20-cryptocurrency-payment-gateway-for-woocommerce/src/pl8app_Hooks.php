<?php

function pl8app_change_cancelled_email_note_subject_line($subject, $order) {
	$subject = 'Order ' . $order->get_id() . ' has been cancelled due to non-payment';

	return $subject;

}

function pl8app_change_cancelled_email_heading($heading, $order) {
	$heading = "Your order has been cancelled. Do not send any cryptocurrency to the payment address.";

	return $heading;
}

function pl8app_change_partial_email_note_subject_line($subject, $order) {
	$subject = 'Partial payment received for Order ' . $order->get_id();

	return $subject;
}

function pl8app_change_partial_email_heading($heading, $order) {
	$heading = 'Partial payment received for Order ' . $order->get_id();

	return $heading;
}

function pl8app_update_database_when_admin_changes_order_status( $orderId, $oldOrderStatus, $newOrderStatus ) {
  
	$paymentAmount = 0.0;
	
	$paymentAmount = get_post_meta($orderId, 'crypto_amount', true);

	// this order was not made by us
	if ($paymentAmount === 0.0 || !$paymentAmount) {

		return;
  }
	

	$paymentRepo = new pl8app_Payment_Repo();

	// If admin updates from needs-payment to has-payment, stop looking for matching transactions
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'processing') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'completed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'processing') {

		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'completed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'paid');
	}

	// If admin updates from has-payment to needs-payment, start looking for matching transactions
	if ($oldOrderStatus === 'processing' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'processing' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'completed' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}
	if ($oldOrderStatus === 'completed' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
	}

	// If admin updates from needs-payment to cancelled, stop looking for matching transactions
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'cancelled') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'pending' && $newOrderStatus === 'failed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'cancelled') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}
	if ($oldOrderStatus === 'on-hold' && $newOrderStatus === 'failed') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
	}

	// If admin updates from cancelled to needs-payment, start looking for matching transactions
	if ($oldOrderStatus === 'cancelled' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'cancelled' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'failed' && $newOrderStatus === 'on-hold') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}
	if ($oldOrderStatus === 'failed' && $newOrderStatus === 'pending') {
		$paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
		$paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
	}

  // WC PREFIX
  // If admin updates from needs-payment to has-payment, stop looking for matching transactions
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-processing') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-completed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-processing') {

    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-completed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'paid');
  }
  

  // If admin updates from has-payment to needs-payment, start looking for matching transactions
  if ($oldOrderStatus === 'wc-processing' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-processing' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-completed' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }
  if ($oldOrderStatus === 'wc-completed' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
  }

  // If admin updates from needs-payment to cancelled, stop looking for matching transactions
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-cancelled') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-pending' && $newOrderStatus === 'wc-failed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-cancelled') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }
  if ($oldOrderStatus === 'wc-on-hold' && $newOrderStatus === 'wc-failed') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'cancelled');
  }

  // If admin updates from cancelled to needs-payment, start looking for matching transactions
  if ($oldOrderStatus === 'wc-cancelled' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-cancelled' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-failed' && $newOrderStatus === 'wc-on-hold') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
  if ($oldOrderStatus === 'wc-failed' && $newOrderStatus === 'wc-pending') {
    $paymentRepo->set_status($orderId, $paymentAmount, 'unpaid');
    $paymentRepo->set_ordered_at($orderId, $paymentAmount, time());
  }
}

function pl8app_add_flash_notice($notice = "", $type = "error", $dismissible = true) {
    // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
    $notices = get_option( "my_flash_notices", array() );
 
    $dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
 
    // We add our new notice.
    array_push( $notices, array( 
            "notice" => $notice, 
            "type" => $type, 
            "dismissible" => $dismissible_text
        ) );
 
    // Then we update the option with our notices array
    update_option("my_flash_notices", $notices );
}
 
/**
 * Function executed when the 'admin_notices' action is called, here we check if there are notices on
 * our database and display them, after that, we remove the option to prevent notices being displayed forever.
 * @return void
 */ 
function pl8app_display_flash_notices() {
    $notices = get_option( "my_flash_notices", array() );
     
    // Iterate through our notices to be displayed and print them.
    foreach ( $notices as $notice ) {
        printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
            esc_attr($notice['type']),
            esc_attr($notice['dismissible']),
            esc_attr($notice['notice'])
        );
    }
 
    // Now we reset our options to prevent notices being displayed forever.
    if( ! empty( $notices ) ) {
        delete_option( "my_flash_notices", array() );
    }

}

function pl8app_load_redux_css($stuff) {

    $cssPath = pl8app_PLUGIN_DIR . '/assets/css/pl8app-redux-settings.css';
    wp_enqueue_style('pl8app-styles', $cssPath, array(), pl8app_VERSION);

}

function pl8app_load_js_css($stuff) {
    $cssPath = pl8app_PLUGIN_DIR . '/assets/css/pl8app-custom-admin.css';
    wp_enqueue_style('pl8app-icon-styles', $cssPath, array(), pl8app_VERSION);
	if (!is_array($_GET)) {
		return;
	}

	if (!array_key_exists('page', $_GET)) {
		return;
	}
		
	$page = sanitize_text_field(trim($_GET['page']));
	
	if ($page === 'pl8apppro_options') {
		$jsPath = pl8app_PLUGIN_DIR . '/assets/js/pl8app-redux-mpk.js';

		if (pl8app_Util::p_enabled()) {
			wp_enqueue_script('pl8app-scripts', $jsPath, array( 'jquery', 'pl8appp-admin-scripts' ), pl8app_VERSION);
        }
        else {        	
        	wp_enqueue_script('pl8app-scripts', $jsPath, array( 'jquery' ), pl8app_VERSION);
        }		
	}

	if($page === 'pl8app_crypto_payment_settings'){
        $cssPath = pl8app_PLUGIN_DIR . '/assets/css/pl8app-crypto-admin.css';
        wp_enqueue_style('pl8app-styles', $cssPath, array(), pl8app_VERSION);

        $jsPath = pl8app_PLUGIN_DIR . '/assets/js/pl8app-crypto-admin.js';
        wp_enqueue_script('pl8app-scripts', $jsPath, array( 'jquery' ), pl8app_VERSION);

    }

}

function pl8app_first_mpk_address_ajax() {
	
		if (!isset($_POST) || !is_array($_POST) || !array_key_exists('mpk', $_POST) || !array_key_exists('cryptoId', $_POST)) {
			return;
		}

		$mpk = sanitize_text_field($_POST['mpk']);
		$cryptoId = sanitize_text_field($_POST['cryptoId']);
		$hdMode = sanitize_text_field($_POST['hdMode']);		
		
		if (!pl8app_Hd::is_valid_mpk($cryptoId, $mpk)) {
			return;
		}
		
		if (!pl8app_Util::p_enabled() && (pl8app_Hd::is_valid_ypub($mpk) || pl8app_Hd::is_valid_zpub($mpk))) {
			$message = 'You have entered a valid Segwit MPK.';
			$message2 = '<a href="https://nomiddlemancrypto.io/extensions/segwit" target="_blank">Segwit MPKs are coming soon!</a>';

			echo json_encode([$message, $message2, '']);
			wp_die();
		}
		else {
			$firstAddress = pl8app_Hd::create_hd_address($cryptoId, $mpk, 0, $hdMode);
			$secondAddress = pl8app_Hd::create_hd_address($cryptoId, $mpk, 1, $hdMode);
			$thirdAddress = pl8app_Hd::create_hd_address($cryptoId, $mpk, 2, $hdMode);

			echo json_encode([$firstAddress, $secondAddress, $thirdAddress]);
			wp_die();
		}
}

function pl8app_filter_gateways($gateways){
    global $woocommerce;

    $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

    foreach (pl8app_Cryptocurrencies::get() as $crypto) {
        if ($pl8appSettings->crypto_selected_and_valid($crypto->get_id())) {
        	$gateways[] = 'pl8app_Gateway';
            return $gateways;
        }
    }


    if (is_checkout()) {
	    unset($gateways['pl8app_Gateway']);
	}
	else {
		$gateways[] = 'pl8app_Gateway';
	}

    return $gateways;
}

/**
 * Show row meta on the plugin screen.
 *
 * @param mixed $links Plugin Row Meta.
 * @param mixed $file  Plugin Base file.
 *
 * @return array
 */

function PCBPGFW_plugin_row_meta( $links, $file){

    if ( 'pl8app-cryptocurrency-bep20-payment-gateway-for-woocommerce/pl8app-cryptocurrency-bep20-payment-gateway-for-woocommerce.php' !== $file ) {
        return $links;
    }

    $row_meta = array(
        'support' => '<a href="' . esc_url( 'https://token.pl8app.co.uk' ) . '" target="_blank" aria-label="' . esc_attr__( 'Visit pl8app support', 'pl8app' ) . '">' . esc_html__( 'Support', 'pl8app' ) . '</a>',
    );

    return array_merge( $links, $row_meta );
}

add_filter( 'plugin_row_meta',  'PCBPGFW_plugin_row_meta' , 10, 2 );

add_filter( 'woocommerce_currencies',  'add_pl8app_token_currency');
add_filter( 'woocommerce_currency_symbol', 'add_pl8app_token_currency_symbol', 10 ,2);

function add_pl8app_token_currency($cw_currency)
{
    $cryptos = pl8app_Cryptocurrencies::get_alpha();;
    $options = get_option('pl8apppro_redux_options', array());
    $selected_cryptos = isset($options['crypto_select']) && is_array($options['crypto_select'])?$options['crypto_select']:array();
    foreach ($cryptos as $crypto) {
    	if(in_array($crypto->get_id(), $selected_cryptos))
        $cw_currency[$crypto->get_id()] = $crypto->get_name();
    }

    return $cw_currency;
}

function add_pl8app_token_currency_symbol($custom_currency_symbol, $custom_currency)
{
    $cryptos = pl8app_Cryptocurrencies::get_alpha();;

    foreach ($cryptos as $crypto) {
        if($crypto->get_id() == $custom_currency)
		{
			$custom_currency_symbol = $crypto->get_name();
			break;
		}
    }
    return $custom_currency_symbol;
}

?>


