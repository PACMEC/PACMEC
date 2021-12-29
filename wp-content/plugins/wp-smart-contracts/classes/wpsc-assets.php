<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include semantic ui JS + CSS + Functions
 */

new WPSC_assets();

class WPSC_assets {

    function __construct() {

        // Load JS Web3 Library in admin
        add_action( 'admin_enqueue_scripts' , [$this, 'loadAssets'], 10, 2 );

        // Load JS Web3 Library in FE
        add_action( 'wp_enqueue_scripts' , [$this, 'loadAssetsFrontEnd'], 10, 2 );

    }

    public static function localizeWPSC($is_a_smart_contract, $is_deployer=false) {

        $option = get_option('etherscan_api_key_option');

        if (WPSC_helpers::valArrElement($option, 'api_key')) {
            $arr["etherscan_api_key"] = $option['api_key'];
        }

        $arr['is_a_smart_contract'] = $is_a_smart_contract;
        $arr['endpoint_url'] = get_rest_url();
        $arr['nonce'] = self::get_rest_nonce();

        if ($is_deployer) {
            $arr['is_deployer'] = true;
        }

        wp_localize_script( 'wp-smart-contracts', 'localize_wpsc',  $arr);

        // add translations for JS
        wp_localize_script('wp-smart-contracts', WPSC_Mustache::createJSObjectNameFromTag('global'), [
            'MANDATORY_FIELD' => __("Mandatory Field", 'wp-smart-contracts'),
            'SELECT_SOCIAL_NET' => __("Please select a Social Network", 'wp-smart-contracts'),
            'SELECT_APPROVERS_PERCENT' => __("Please select approvers percentage ", 'wp-smart-contracts'),
            'PROFILE_LINK' => __("Please write your profile link", 'wp-smart-contracts'),
            'ERC20_RECEIVE_TOKEN'  => __("Please write the address of all tokens, or remove the row", 'wp-smart-contracts'),
            'ERC20_RECEIVE_RATE'  => __("Please write the rate for all tokens, or remove the row", 'wp-smart-contracts'),
            'ERC20_RECEIVE_RATE_INT'  => __("The rate must be a positive integer for all tokens", 'wp-smart-contracts'),
            'CONFIRM_REMOVE_SOCIAL' => __("Are you sure you want to delete this social network?", 'wp-smart-contracts'),
            'CODE_COPIED' => __("Code copied to clipboard!", 'wp-smart-contracts'),
            'WRITE_ADDRESS' => __("Please write the address of the Smart Contract you want to load", 'wp-smart-contracts'),
        ]);

    }

    public function loadAssets($hook) {

        // creatting or editing a coin flag
        $is_a_smart_contract = "false";
        $is_edit = false;

        if ( ('edit.php' == $hook) or
             ('post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"])) or
             ('post-new.php' == $hook) or 
             ('upload.php' == $hook)
        ) {
            $is_edit = true;
        }

        wp_enqueue_script( 'wpsc-notices', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpic-notice.js' );

        // check if we are editing or adding a smart contract
        if (
                (
                    'post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"]) and (
                        get_post_type($post_id) == "coin" or
                        get_post_type($post_id) == "crowdfunding"
                    )
                ) or
                (
                    'post-new.php' == $hook and (
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "coin" OR 
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "crowdfunding" OR 
                        WPSC_Metabox::cleanUpText($_GET["post_type"]) == "ico"
                    )
                )
        ) {
            $is_a_smart_contract = "true";
        }

        if (
            ('post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"]) and (get_post_type($post_id) == "nft-collection")) or
            ('post-new.php' == $hook and (WPSC_Metabox::cleanUpText($_GET["post_type"]) == "nft-collection"))
        ) {
            wp_enqueue_style( 'wp-color-picker' );
            $wpsc_deps = ['wp-color-picker'];
        } else {
            $wpsc_deps = [];
        }

        // queue for all admin area        
        wp_enqueue_script(  'web3', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/web3.js' );
        wp_enqueue_script(  'wp-smart-contracts', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc.js', $wpsc_deps );

        self::localizeWPSC($is_a_smart_contract);

        // enqueue it only if we are creating or editing a coin
        if ($is_edit) {

            if (
                ('post.php' == $hook and $post_id = WPSC_Metabox::cleanUpText($_GET["post"]) and (get_post_type($post_id) == "nft")) or
                ('post-new.php' == $hook and (WPSC_Metabox::cleanUpText($_GET["post_type"]) == "nft")) or
                ('upload.php' == $hook)
            ) {
                wp_enqueue_media();
                wp_register_script( 'nft-js', dirname( plugin_dir_url( __FILE__ )) . '/assets/js/nft.js' );
                wp_enqueue_script( 'nft-js' );
            }            
            
            wp_enqueue_script( 'blockies', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/blockies.min.js' );
            wp_enqueue_script( 'copytoclipboard', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/copytoclipboard.js' );
            wp_localize_script( 'copytoclipboard', 'copied', ["str" => __('Copied!', 'wp-smart-contracts')] );
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_script( 'zoom-qr', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/zoom-qr.js' );
            wp_enqueue_script( 'wpsc-google-prettify', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/prettify/run_prettify.js?autoload=true&skin=desert' );
            wp_localize_script( 'wpsc-google-prettify', 'wpsc_plugin_path', ["wpsc_plugin_path" => dirname( plugin_dir_url( __FILE__ ) )] );

            wp_localize_script( 'nft-js', 'wpApiSettings', array(
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' )
            ));        


            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);

        }

        if ('settings_page_etherscan-api-key-setting-admin' == $hook) {
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);
        }

        // enqueue in all admin pages
        wp_enqueue_style( 'wp-smart-contracts-admin-bar', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/wp-admin-bar.css');

        // Load wp admin bar
        add_action('admin_bar_menu', [$this, 'addToolbar'], 999);

    }

    // load wp admin toolbar with metamask info
    public function addToolbar($wp_admin_bar) {
        $m = new Mustache_Engine;
        $wp_admin_bar->add_node( [
            'id'    => 'wp-smart-contracts',
            'title' => 'WPSmartContracts'
        ]);
    }

    public static function isEthereumNetwork($net) {
        return ($net==1 || $net==3 || $net==4 || $net==5 || $net==42);
    }

    static public function getCollectionID() {

        $id_url = (int) $_GET["id"];
        if ($id_url and get_post_type($id_url)=="nft-collection") return $id_url;

        $id = get_the_ID();
        if (self::isNFT()) return get_post_meta($id, 'wpsc_item_collection', true);;
        if (get_post_type($id)=="nft-collection") return $id;

        return false;
    }
    

    static public function isNFT() {
        $id = get_the_ID();
        $content = get_post_field('post_content', $id);
        if (get_post_type($id)=="nft" or has_shortcode($content, 'wpsc_nft') or has_shortcode($content, 'wpsc_nft_mint')) {
            return true;
        } else {
            return false;
        }
    }

    static public function isNFTCollection() {
        // is a NFT Collection?
        $id = get_the_ID();
        if (isset($id) and (get_post_type($id)=="nft-collection" or has_shortcode(get_post_field('post_content', $id), 'wpsc_nft_collection'))) {
            return true;
        } else {
            return false;
        }
    }

    static public function isNFTTax() {
        if (is_tax( 'nft-taxonomy') or is_tax( 'nft-tag')) {
            return true;
        } else {
            return false;
        }
    }

    public function loadAssetsFrontEnd() {
        
        // flag for is a contract
        $is_a_token = false;
        $is_a_crowd = false;
        $is_a_ico = false;
        $is_a_scanner = true;
        $is_a_stake = false;

        $id = get_the_ID();

        // is a coin?
        if (get_post_type($id)=="coin") {
            $is_a_token = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_coin')) {
            $is_a_token = true;
        }

        // is a crowd?
        if (get_post_type($id)=="crowdfunding") {
            $is_a_crowd = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_crowdfunding')) {
            $is_a_crowd = true;
        }

        // is an ico?
        if (get_post_type($id)=="ico") {
            $is_a_ico = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_ico')) {
            $is_a_ico = true;
        }

        $is_a_nft = self::isNFT();

        $is_a_nft_collection = self::isNFTCollection();

        // is a stake?
        if (get_post_type($id)=="staking") {
            $is_a_stake = true;
        } elseif (has_shortcode(get_post_field('post_content', $id), 'wpsc_staking')) {
            $is_a_stake = true;
        }
        
        // is a QR Scanner?
        if (has_shortcode(get_post_field('post_content', $id), 'wpsc_qr_scanner')) {
            $is_a_scanner = true;
        }

        // load global assets for all contracts
        if ($is_a_token or $is_a_crowd or $is_a_ico or $is_a_scanner or $is_a_nft or $is_a_stake) {

            wp_enqueue_script( 'copytoclipboard', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/copytoclipboard.js' );
            wp_localize_script( 'copytoclipboard', 'copied', ["str" => __('Copied!', 'wp-smart-contracts')] );
            wp_enqueue_script( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.js', ['jquery'] );
            wp_enqueue_script( 'zoom-qr', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/zoom-qr.js' );
            wp_enqueue_script( 'blockies', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/blockies.min.js' );
            wp_enqueue_script( 'wpsc-google-prettify', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/prettify/run_prettify.js?autoload=true&skin=desert' );
            wp_localize_script( 'wpsc-google-prettify', 'wpsc_plugin_path', ["wpsc_plugin_path" => dirname( plugin_dir_url( __FILE__ ) )] );

            // token specific assets
            if ($is_a_token) {
                
                wp_enqueue_script( 'wpsc-fe', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe.js' );

                $arr_wpsc_fe["endpoint_url"] = get_rest_url();
                $arr_wpsc_fe["nonce"] = self::get_rest_nonce();
                $arr_wpsc_fe["is_block_explorer"] = $is_a_token?"true":"false";

                $wpsc_adv_burn = get_post_meta($id, 'wpsc_adv_burn', true);
                $wpsc_adv_pause = get_post_meta($id, 'wpsc_adv_pause', true);
                $wpsc_adv_mint = get_post_meta($id, 'wpsc_adv_mint', true);

                if ($wpsc_adv_burn) $arr_wpsc_fe["wpsc_adv_burn"] = $wpsc_adv_burn;
                if ($wpsc_adv_pause) $arr_wpsc_fe["wpsc_adv_pause"] = $wpsc_adv_pause;
                if ($wpsc_adv_mint) $arr_wpsc_fe['wpsc_adv_mint'] = $wpsc_adv_mint;

                // get the first page defined as qr-scanner
                if ($page = self::getQrScanner()) {
                    $arr_wpsc_fe['qr_scanner_page'] = $page;
                }

                wp_localize_script( 'wpsc-fe', 'localize_var', $arr_wpsc_fe );

            }

            if ($network_id = get_post_meta($id, 'wpsc_network', true) and $network_array = WPSC_helpers::getNetworks()) {
                $network_name = $network_array[$network_id]["name"];
                $coin_symbol = $network_array[$network_id]["coin-symbol"];
            }

            // crowd specific assets
            if ($is_a_crowd) {
                wp_enqueue_script( 'wpsc-fe-crowd', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-crowd.js' );

                if (isset($network_name) and $network_name) {
                    $arr_wpsc_fe_crowd['wpsc_network_name'] = $network_name;
                }

                $arr_wpsc_fe_crowd["endpoint_url"] = get_rest_url();
                $arr_wpsc_fe_crowd["nonce"] = self::get_rest_nonce();
                $arr_wpsc_fe_crowd["is_crowd"] = $is_a_crowd?"true":"false";
                if ($page = self::getQrScanner()) {
                    $arr_wpsc_fe_crowd['qr_scanner_page'] = $page;
                }

                wp_localize_script( 'wpsc-fe-crowd', 'localize_var', $arr_wpsc_fe_crowd );

            }

            // ICO specific assets
            if ($is_a_ico) {

                wp_enqueue_script( 'wpsc-fe-ico', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-ico.js' );

                $arr_wpsc_fe_ico['wpsc_adv_hard'] = ($id and get_post_meta($id, 'wpsc_adv_hard', true))?get_post_meta($id, 'wpsc_adv_hard', true):'0';
                $arr_wpsc_fe_ico['wpsc_adv_cap'] = ($id and get_post_meta($id, 'wpsc_adv_cap', true))?get_post_meta($id, 'wpsc_adv_cap', true):'0';
                $arr_wpsc_fe_ico['endpoint_url'] = get_rest_url();
        
                if (isset($network_name) and $network_name) {
                    $arr_wpsc_fe_ico['wpsc_network_name'] = $network_name;
                }
                if ($page = self::getQrScanner()) {
                    $arr_wpsc_fe_ico['qr_scanner_page'] = $page;
                }

                wp_localize_script( 'wpsc-fe-ico', 'localize_var', $arr_wpsc_fe_ico );

            }

            if (isset($is_a_stake) and $is_a_stake) {

                wp_enqueue_script( 'wpsc-fe-stake', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-stake.js' );

                if (isset($network_name) and $network_name) {
                    $arr_wpsc_fe_stake['wpsc_network_name'] = $network_name;
                }

                $infura = get_option('etherscan_api_key_option');

                $arr_wpsc_fe_stake['INFURA_API_KEY'] = trim($infura["infura_api_key"]);
                $arr_wpsc_fe_stake['INFURA_MNEMONIC'] = trim($infura["infura_mnemonic"]);
                $arr_wpsc_fe_stake['is_ethereum'] = self::isEthereumNetwork($network_id);

                wp_localize_script( 'wpsc-fe-stake', 'localize_var', $arr_wpsc_fe_stake );

            }

            self::loadNFTTheme();

            // NFT specific assets
            if ($is_a_nft) {
                wp_enqueue_script( 'wpsc-fe-nft', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/js/wpsc-fe-nft.js', ['jquery', 'wp-smart-contracts-semantic'] );

                $arr_wpsc_fe_nft['endpoint_url'] = get_rest_url();
                $arr_wpsc_fe_nft['nonce'] = self::get_rest_nonce();
                
                if (WPSC_helpers::valArrElement($_GET, 'nft_id') and get_post_meta($id, "wpsc_is_nft_minter", true)) {
                    $nft_id = (int) $_GET["nft_id"];
                    if ($nft_id) {
                        $arr_wpsc_fe_nft['the_permalink_nft'] = get_the_permalink($nft_id);
                    }
                }
                
                if (isset($network_name) and $network_name) {
                    $arr_wpsc_fe_nft['wpsc_network_name'] = $network_name;
                    $arr_wpsc_fe_nft['wpsc_coin_symbol'] = $coin_symbol;
                }

                $infura = get_option('etherscan_api_key_option');
                
                $arr_wpsc_fe_nft['INFURA_API_KEY'] = trim($infura["infura_api_key"]);
                $arr_wpsc_fe_nft['INFURA_MNEMONIC'] = trim($infura["infura_mnemonic"]);
                $arr_wpsc_fe_nft['is_ethereum'] = self::isEthereumNetwork($network_id);
                
                // get the first page defined as qr-scanner
                if ($page = self::getQrScanner()) {
                    $arr_wpsc_fe_nft['qr_scanner_page'] = $page;
                }

                wp_localize_script( 'wpsc-fe-nft', 'localize_var', $arr_wpsc_fe_nft);

                wp_localize_script( 'wpsc-fe-nft', 'wpApiSettings', array(
                    'root' => esc_url_raw( rest_url() ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'post_id' => $id
                ));        

            }

            if ($is_a_nft_collection or self::isNFTTax()) {
                if (isset($network_name) and isset($coin_symbol)) {
                    self::loadNFTMy($network_name, $coin_symbol);
                }
            }

            wp_enqueue_style( 'wp-smart-contracts-semantic', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/semantic/dist/semantic.min.css');
            wp_enqueue_style( 'wp-smart-contracts-styles', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/css/styles.css', ['wp-smart-contracts-semantic']);

        }

    }

    static public function loadNFTMy($network_name=false, $coin_symbol = false) {
        
        wp_register_script( 'nft-js-my', dirname( plugin_dir_url( __FILE__ )) . '/assets/js/wpsc-fe-nft-my.js', ['jquery'] );
        self::loadNFTTheme();
        wp_enqueue_script( 'nft-js-my' ); 

        if (!isset($network_name) or !$network_name) {
            if (WPSC_helpers::valArrElement($_GET, 'id')) {
                $collection_id = (int) $_GET["id"];
                if ($network_id = get_post_meta($collection_id, 'wpsc_network', true) and $network_array = WPSC_helpers::getNetworks()) {
                    $network_name = $network_array[$network_id]["name"];
                }
            }
        }

        if (!isset($coin_symbol) or !$coin_symbol) {
            if ((!isset($collection_id) or !$collection_id) and WPSC_helpers::valArrElement($_GET, 'id')) $collection_id = (int) $_GET["id"];
            if (isset($collection_id) and $collection_id) {
                if ((!isset($network_id) or !$network_id)) $network_id = get_post_meta($collection_id, 'wpsc_network', true);
            }
            if (!isset($network_array) or !$network_array) $network_array = WPSC_helpers::getNetworks();
            if (isset($network_id) and isset($network_array) and $network_array) {
                $coin_symbol = $network_array[$network_id]["coin-symbol"];
            }
        }

        $infura = get_option('etherscan_api_key_option');

        $arr_nft_my['wpsc_network_name'] = $network_name;
        $arr_nft_my['wpsc_coin_symbol'] = $coin_symbol;
        $arr_nft_my['INFURA_API_KEY'] = trim($infura["infura_api_key"]);
        $arr_nft_my['INFURA_MNEMONIC'] = trim($infura["infura_mnemonic"]);

        wp_localize_script( 'nft-js-my', 'localize_var', $arr_nft_my);

        wp_localize_script( 'nft-js-my', 'wpApiSettings', array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ));    
    }

    static private function loadNFTTheme() {
        if ($path = WPSC_Mustache::getThemePath(true)) {
            wp_enqueue_style( 'wpsc-fe-nft-theme-css', $path . 'css/main.css');
            wp_enqueue_script( 'wpsc-fe-nft-theme', $path . 'js/main.js' );        
        }
    }

    // get the wp rest nonce with the proper separator & or ?
    private static function get_rest_nonce() {

        $nonce = wp_create_nonce('wp_rest');
        
        if (strpos(get_rest_url(), '?')===false) {
            return urlencode("?_wpnonce=" . $nonce);
        } else {
            return urlencode("&_wpnonce=" . $nonce);
        }

    }
    
    static public function getQrScanner() {
        return self::getPage("wpsc_is_scanner");
    }

    static public function getNFTMintPage() {
        return self::getPage("wpsc_is_nft_minter");
    }

    static public function getNFTMyItemsPage() {
        return self::getPage("wpsc_is_nft_my_items");
    }

    static public function getNFTMyBidsPage() {
        return self::getPage("wpsc_is_nft_my_bids");
    }

    static public function getNFTAuthorsPage() {
        return self::getPage("wpsc_is_nft_author");
    }

    static private function getPage($meta) {

        $pages = get_pages([
            'meta_key' => '_wp_page_template',
            'meta_value' => 'wpsc-clean-template.php'
        ]);

        if (is_array($pages)) {
            foreach($pages as $page) {
                if (is_object($page) and get_post_meta($page->ID, $meta, true)) {
                    return get_permalink($page->ID);
                }    
            }
        }

        return false;

    }

}

