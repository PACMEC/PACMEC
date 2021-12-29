<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include semantic ui JS + CSS + Functions
 */

class WPSC_helpers {

	// use to avoid notices when accesing an array element
	static public function valArrElement($arr=null, $elem=null) {
		if (!is_array($arr)) return false;
		if (!$elem) return false;
		if (!array_key_exists($elem, $arr)) return false;
		return true;
	}

	// get network information from json file
	static function getNetworks() {
	    if (file_exists($json_file = dirname(dirname(__FILE__)).'/assets/json/networks.json') and 
	    	is_array( $arr = json_decode( file_get_contents($json_file), true ) ) ) {
	    	return $arr;
		} else {
			return false;
		}
	}

	// format a number with 18 decimals and the proper number separators
    static public function formatNumber($num) {

    	// convert to float
    	$num = floatval($num);

    	// validate number of decimals to show, use 4 as default
    	if (!is_numeric($ndts = WPSCSettingsPage::numberOfDecimalsToShow())) {
    		$ndts = 4;
    	}

    	// add thousands and decimals separators
        $nf = number_format($num, $ndts, WPSCSettingsPage::numberFormatDecimals(), WPSCSettingsPage::numberFormatThousands());

        return $nf;

    }

    // create a page with QR Scanner and NFT minter if not already created
    static public function createPluginPages() {

    	if (WPSC_assets::getQrScanner()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'WPSC QR Scanner',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_qr_scanner]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_scanner', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTMintPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT Mint',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_mint]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_minter', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTMyItemsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT My Items',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_my_items]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_my_items', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTAuthorsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT Authors',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_author]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_author', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

		if (WPSC_assets::getNFTMyBidsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT My Bids',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_my_bids]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_my_bids', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    }

    // return the short version of an Ethereum address
	public static function shortify($address, $ultra=false) {
		if ($ultra) {
			return substr($address, 0, 4) . '...' . substr($address, -2);
		} else {
			return substr($address, 0, 6) . '...' . substr($address, -4);
		}
	}

	public static function languages() {
		load_plugin_textdomain( 'wp-smart-contracts', false, basename( dirname( __DIR__ ) ) . '/languages/' );
	}

	public static function renderWPICInfo() {
	    $m = new Mustache_Engine;
	    return $m->render(
	      WPSC_Mustache::getTemplate('metabox-wpic-info'), 
	      [
	        "title" => __('Scale Ethereum', 'wp-smart-contracts'),
			"logo" => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/xdai.png',
	        "description_1" => __('Layer 2 Solutions supported by WPSmartContracts', 'wp-smart-contracts'),
	        "description_2" => __('Now you can deploy all smart contracts flavors in four blockchains.', 'wp-smart-contracts'),
	        "feature_1" => __('Ethereum', 'wp-smart-contracts'),
	        "feature_2" => __('xDai Chain', 'wp-smart-contracts'),
	        "feature_3" => __('Binance Smart Chain', 'wp-smart-contracts'),
	        "feature_4" => __('Polygon (previously Matic)', 'wp-smart-contracts'),
	        "feature_5" => __('Easy deploy with lower fees', 'wp-smart-contracts'),
			"blockchain_img" => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/blocks.png',
	        "claim" => __('Claim 5,000 WPIC', 'wp-smart-contracts'),
	        "claim-note" => __('0.1 Ξ', 'wp-smart-contracts'),
	        "learn" => __('Learn more', 'wp-smart-contracts'),
	      ]
	    );
	}

	static public function nativeCoinName($wpsc_network) {

		switch ($wpsc_network) {
			case 77:
			case 100:
				return "xDai";
				break;			
			case 97:
			case 56:
				return "BNB";
				break;
			case 80001:
			case 137:
				return "Matic";
				break;
			default:
				return "Ether";
				break;
		}

	}

	static public function getIdFromShortcodes() {

		$the_id = get_the_ID();

		$data = get_post_field("post_content", $the_id);

		foreach(['wpsc_coin', 'wpsc_crowdfunding', 'wpsc_ico', 'wpsc_staking'] as $shortcode) {

			preg_match("/\[$shortcode (.+?)\]/", $data, $dat);
		
			$dat = array_pop($dat);
			$dat= explode(" ", $dat);
			$params = array();
			foreach ($dat as $d){
				if ($d) {
					list($opt, $val) = explode("=", $d);
					$params[$opt] = trim($val, '"');	
				}
			}
		
			if (WPSC_helpers::valArrElement($params, "id") and $params["id"]) {
				$the_id = $params["id"];
				break;
			}
		
		}
		
		return $the_id;
	}

}

/**
 * Warnings in wṕ-admin
 */

add_action('admin_notices', function () {

	// check Infura settings

	$options = get_option('etherscan_api_key_option');
	$infura_api_key = (WPSC_helpers::valArrElement($options, "infura_api_key") and !empty($options["infura_api_key"]))?$options["infura_api_key"]:false;

	if (empty(trim($infura_api_key))) {
		echo '<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3><p>To use the WP Smart Contracts plugin properly you need to setup a free <a href="https://infura.io/" target="_blank">Infura Project ID</a> in <a href="'. get_admin_url() . 'options-general.php?page=etherscan-api-key-setting-admin">WP Smart Contracts Settings</a> in your WordPress install</p><p>Otherwise your site may not allow users to properly interact with your Smart Contract if they do not have Metamask installed</p>
		</div>';
	}

	// check that rest api is alive

	$endpoint = get_rest_url(null, 'wpsc/v1/ping');

	$response = wp_remote_get( $endpoint, null );

	if (!WPSC_helpers::valArrElement($response, "response") or !WPSC_helpers::valArrElement($response["response"], "code") or $response["response"]["code"] != "200") {
		
		$get_rest_url = get_rest_url(null, '');

		echo <<<HELP
		<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3>
			<p>Looks like the <a href="https://developer.wordpress.org/rest-api/" target="_blank">WP Rest API</a> is not working properly in your WordPress installation</p>
			<p>Please check that the URL: <a href="$get_rest_url" target="_blank">$get_rest_url</a> is not failing</p>
			<p>For more information please visit: <a href="https://wordpress.org/support/topic/404-error-on-coin-page-already-created-the-coin-on-mainnet/" target="_blank">our support page</a></p>
		</div>
HELP;
	}

	// NEWS

	if (!WPSC_helpers::valArrElement($_COOKIE, "stake_notice_hide")) {

		$wpsc_logo = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png';
		$wpsc_ube = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/ube-card.png';

		$stake_admin_url = get_admin_url() . "/post-new.php?post_type=staking";

		echo <<<BLOCKCHAIN
		<div id="wpic-notification" class="notice notice-info is-dismissible">
		<p><img src="$wpsc_logo" style="max-width: 300px;"></p>
		<h1>New NFT flavors</h1>
		<p>
			<img src="$wpsc_ube" style="max-width: 250px;">
		</p>
		<p>Check the new smart contracts we have for you</p>	
		<p>» Ube: ERC-20 Stakings</p>
		<p><strong><a href="$stake_admin_url">Go to ERC-20 Stakings now »</a></strong></p>
		<p style="color: #777;"><input type="checkbox" id="wpic-no-show"> Got it, Thanks!. Please don't show this again.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	 </div>
BLOCKCHAIN;
	}

});

/**
 * Load i18n files
 */
add_action( 'plugins_loaded', ['WPSC_helpers', 'languages'] );


/**
 * One time notification
 */
class OneTimeNotifications {

    public static $_notices  = array();

    public function __construct() {
		add_action( 'shutdown', array( $this, 'save_errors' ) );
    }

    public static function add_notice( $title, $text ) {
        self::$_notices[] = [$title, $text];
    }

    public function save_errors() {
        update_option( 'wpsc_custom_notices', self::$_notices );
    }

    public function output_errors() {
        $errors = maybe_unserialize( get_option( 'wpsc_hide_custom_notices' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="mc_notices" class="error notice is-dismissible">';

			echo '<h3>WP Smart Contracts change log</h3>';

            foreach ( $errors as $error ) {
				if ($error[0]) echo '<p><b>&raquo; ' . wp_kses_post( $error[0] ) . '</b>';
                if ($error[1]) echo '<br>' . wp_kses_post( $error[1] ) . '</p>';
            }

            echo '</div>';

			update_option( 'wpsc_hide_custom_notices', 1 );

		}
    }

}

if (!get_option( 'wpsc_onetime_notice_1' )) {
	add_action( 'shutdown', function () {
		echo '<div class="notice notice-info is-dismissible">';
		echo '<h3>WP Smart Contracts change log</h3>';
		foreach ([
			["We ♥ WP5.5", "Now WPSmartContracts is compatible with WordPress 5.5"],
			["Uniswap is in da house!", "Now you can add your token to Uniswap and include it in the Exchanges section"],
			["Because good ideas needs your support!", "Starting January 1, 2021 deploy fees may apply, visit <a href=\"https://wpsmartcontracts.com\" target=\"_blank\">wpsmartcontracts.com</a> for more information"]
		] as $notice ) {
			echo '<p><b>&raquo; ' . $notice[0] . '</b>';
			echo '<br>' . $notice[1] . '</p>';
		}
		echo '</div>';
		update_option( 'wpsc_onetime_notice_1', 1 );
	});
}
