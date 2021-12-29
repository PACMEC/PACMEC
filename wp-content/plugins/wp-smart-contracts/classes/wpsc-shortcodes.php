<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Define and render shortcodes
 */

new WPSC_Shortcodes();

class WPSC_Shortcodes {

    private $templates;

    function __construct() {
        add_shortcode( 'wpsc_coin', [$this, "coin"] );
        add_shortcode( 'wpsc_qr_scanner', [$this, "qrScanner"] );
        add_shortcode( 'wpsc_crowdfunding', [$this, "crowdfunding"] );
        add_shortcode( 'wpsc_ico', [$this, "ico"] );
        add_shortcode( 'wpsc_nft_collection', [$this, "nftCollection"] );
        add_shortcode( 'wpsc_nft_taxonomy', [$this, "nftTaxonomy"] );
        add_shortcode( 'wpsc_nft', [$this, "nft"] );
        add_shortcode( 'wpsc_nft_mint', [$this, "nftMint"] );
        add_shortcode( 'wpsc_nft_my_items', [$this, "nftMyItems"] );
        add_shortcode( 'wpsc_nft_my_bids', [$this, "nftMyBids"] );
        add_shortcode( 'wpsc_nft_author', [$this, "nftAuthor"] );
        add_shortcode( 'wpsc_staking', [$this, "staking"] );
    }

    public function qrScanner($params) {
        
        $atts = [
            "qr-scanner" => plugins_url( "assets/js/qr-scanner.min.js", dirname(__FILE__) ),
            "qr-scanner-worker" => plugins_url( "assets/js/qr-scanner-worker.min.js", dirname(__FILE__) ),
            "align-camera" => __('Align the QR code with the camera', 'wp-smart-contracts')
        ];

        if (array_key_exists('input', $_GET) and $input = $_GET['input'] and $input_sanitized = sanitize_text_field( $input )) {
            $atts["input-name"] = $input_sanitized;
        }

        $m = new Mustache_Engine;
        return $m->render(WPSC_Mustache::getTemplate('qr-scanner'), $atts);

    }

    public function coin($params) {

        $xdai_block_explorer = $xdai = null;

        $the_id = self::getPostID($params);

        if (is_array($params) and array_key_exists('id', $params) and $params["id"]) {
            $title = get_the_title(absint($params["id"]));
            $the_id = $params['id'];
        }

        if (!$the_id) {
            $the_id = get_the_ID();
        }

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);


        if ($wpsc_network==77 or    // xDai testnet
            $wpsc_network==100 or   // xDai mainnet
            $wpsc_network==97 or    // BSC Testnet
            $wpsc_network==56 or    // xDai mainnet
            $wpsc_network==80001 or // Matic Testnet
            $wpsc_network==137) {   // Matic Mainnet
            $networks = WPSC_helpers::getNetworks();

            $xdai_block_explorer = $networks[$wpsc_network]["url2"]."address/".$wpsc_contract_address;
            $xdai=true;
        }

        $wpsc_social_icon = get_post_meta($the_id, 'wpsc_social_icon', true);
        $wpsc_social_link = get_post_meta($the_id, 'wpsc_social_link', true);
        $wpsc_social_name = get_post_meta($the_id, 'wpsc_social_name', true);

        $m = new Mustache_Engine;

        // initialization
        $wpsc_flavor        = null;
        $wpsc_forkdelta     = null;
        $wpsc_uniswap     = null;
        $wpsc_adv_burn      = null;
        $wpsc_adv_pause     = null;
        $wpsc_adv_mint      = null;
        $wpsc_coin_name     = null;
        $wpsc_coin_symbol   = null;
        $wpsc_coin_decimals = null;
        $wpsc_total_supply  = null;
        $wpsc_adv_cap       = null;
        $atts = [];

        // show contract
        if ($wpsc_contract_address) {

            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
            $wpsc_forkdelta     = get_post_meta($the_id, 'wpsc_forkdelta', true);
            $wpsc_uniswap       = get_post_meta($the_id, 'wpsc_uniswap', true);

            $wpsc_adv_burn      = get_post_meta($the_id, 'wpsc_adv_burn', true);
            $wpsc_adv_pause     = get_post_meta($the_id, 'wpsc_adv_pause', true);
            $wpsc_adv_mint      = get_post_meta($the_id, 'wpsc_adv_mint', true);
            $wpsc_coin_name     = get_post_meta($the_id, 'wpsc_coin_name', true);
            $wpsc_coin_symbol   = get_post_meta($the_id, 'wpsc_coin_symbol', true);
            $wpsc_coin_decimals = get_post_meta($the_id, 'wpsc_coin_decimals', true);
            $wpsc_total_supply  = WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_total_supply', true));

            $the_cap = get_post_meta($the_id, 'wpsc_adv_cap', true);
            if ($the_cap) {
                $wpsc_adv_cap = WPSC_helpers::formatNumber($the_cap);
            } else {
                $wpsc_adv_cap = __('Unlimited', 'wp-smart-contracts');
            }

            $tokenInfo = [
                "type" => $wpsc_flavor,
                "symbol" => $wpsc_coin_symbol,
                "name" => $wpsc_coin_name,
                "decimals" => $wpsc_coin_decimals,
                "supply" => $wpsc_total_supply,
                "size" => "mini",
                "symbol_label" => __('Symbol', 'wp-smart-contracts'),
                "name_label" => __('Name', 'wp-smart-contracts'),
                "decimals_label" => __('Decimals', 'wp-smart-contracts'),
                "initial_label" => __('Initial Supply', 'wp-smart-contracts'),
                "burnable_label" => __('Burnable', 'wp-smart-contracts'),
                "mintable_label" => __('Mintable', 'wp-smart-contracts'),
                "max_label" => __('Max. cap', 'wp-smart-contracts'),
                "pausable_label" => __('Pausable', 'wp-smart-contracts'),    
            ];
            if ($wpsc_flavor=="chocolate") {
                $tokenInfo["color"] = "brown";
                $tokenInfo["cap"] = $wpsc_adv_cap;
                if ($wpsc_adv_burn) $tokenInfo["burnable"] = true;
                if ($wpsc_adv_mint) $tokenInfo["mintable"] = true;
                if ($wpsc_adv_pause) $tokenInfo["pausable"] = true;
            }
            if ($wpsc_flavor=="vanilla") $tokenInfo["color"] = "yellow";
            if ($wpsc_flavor=="pistachio") $tokenInfo["color"] = "olive";
            $tokenInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

            $atts = [
                'ethereum-network' => $network_val,
                'ethereum-color' => $color,
                'ethereum-icon' => $icon,
                'contract-address' => $wpsc_contract_address,
                'etherscan' => $etherscan,
                'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
                'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
                'owner-text' => __('Owner Account', 'wp-smart-contracts'),
                'token-name' => ucwords(get_post_meta($the_id, 'wpsc_coin_name', true)),
                'token-symbol' => strtoupper(get_post_meta($the_id, 'wpsc_coin_symbol', true)), 
                'qr-code' => $wpsc_qr_code,
                'blockie' => $wpsc_blockie,
                'blockie-owner' => $wpsc_blockie_owner,
                'token-info' => $m->render(WPSC_Mustache::getTemplate('token-info'), $tokenInfo),
                'token-logo' => get_the_post_thumbnail_url($the_id),
                'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
                'txid' => $wpsc_txid,
                'txid-short' => WPSC_helpers::shortify($wpsc_txid),
                'owner' => $wpsc_owner,
                'owner-short' => WPSC_helpers::shortify($wpsc_owner)
            ];

            if ($wpsc_txid) {
                $atts["txid-exists"] = true;
            }

        }

        $social_networks = '';
        if (is_array($wpsc_social_link)) {
            foreach ($wpsc_social_link as $sn_i => $social_link) {
                $social_networks .= $m->render(WPSC_Mustache::getTemplate('coin-view-social-networks'), [
                    'link' => $social_link,
                    'icon' => $wpsc_social_icon[$sn_i]
                ]);
            }                   
        }

        $block_explorer_atts = [
            "xdai" => $xdai,
            "xdai_block_explorer" => $xdai_block_explorer,
            "xdai_block_explorer_label" => __('Go to Block Explorer', 'wp-smart-contracts'),
            'block-explorer' => __('Block Explorer', 'wp-smart-contracts'),
            'search-placeholder' => __('Search by Address or Txhash', 'wp-smart-contracts'),
            'transfers' => __('Transfers', 'wp-smart-contracts'),
            'holders' => __('Holders', 'wp-smart-contracts'),
            'page' => __('Page', 'wp-smart-contracts'),
            'date' => __('Date', 'wp-smart-contracts'),
            'from' => __('From', 'wp-smart-contracts'),
            'to' => __('To', 'wp-smart-contracts'),
            'amount_tx' => __('Amount and Transaction', 'wp-smart-contracts'),
            'value' => __('Value', 'wp-smart-contracts'),
            'previous' => __('Previous', 'wp-smart-contracts'),
            'next' => __('Next', 'wp-smart-contracts'),
            'updated' => __('Synced with blockchain every minute', 'wp-smart-contracts'),
            'account-url' => str_replace('acc-add-here', '', home_url() . esc_url( add_query_arg( 'acc', 'acc-add-here' ) ) ),
            'url' => get_permalink(),
            'etherscan' => $etherscan,
            'subdomain' => strtolower($network_val),
            'contract' => $wpsc_contract_address,
            'network' => $wpsc_network,
            'total_supply' => __('Total supply', 'wp-smart-contracts'),
            'symbol' => $wpsc_coin_symbol,
            'internal-transactions' => __('Internal Transactions', 'wp-smart-contracts'),
            'transactions' => __('Transactions', 'wp-smart-contracts'),
        ];

        $the_token_symbol = WPSC_helpers::valArrElement($atts, 'token-symbol')?$atts["token-symbol"]:null;

        $atts_coin_view_token = [
            'blockie' => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            'token-name' => WPSC_helpers::valArrElement($atts, 'token-name')?$atts["token-name"]:null,
            'token-symbol' => $the_token_symbol,
            'color' => $color,
            'icon' => $icon,
            'ethereum-network' => WPSC_helpers::valArrElement($atts, 'ethereum-network')?$atts["ethereum-network"]:null,
            'token-info' => WPSC_helpers::valArrElement($atts, 'token-info')?$atts["token-info"]:null,
        ];

        $atts_coin_view_addresses = [
            "addresses" => __('Addresses', 'wp-smart-contracts'),
            "contract-address" => WPSC_helpers::valArrElement($atts, 'contract-address')?$atts["contract-address"]:null,
            "token-symbol" => $the_token_symbol,
            "wpsc-add-token-to-metamask" => $m->render(
                WPSC_Mustache::getTemplate('add-token-to-metamask'), [
                    "network" => $network_val,
                    "contract-address" => WPSC_helpers::valArrElement($atts, 'contract-address')?$atts["contract-address"]:null,
                    "token-symbol" => $the_token_symbol,
                    "fox" => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) )
                ]
            ),
            "blockie"                   => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            "contract-address-text"     => WPSC_helpers::valArrElement($atts, 'contract-address-text')?$atts["contract-address-text"]:null,
            "contract-address-short"    => WPSC_helpers::valArrElement($atts, 'contract-address-short')?$atts["contract-address-short"]:null,
            "qr-code"                   => WPSC_helpers::valArrElement($atts, 'qr-code')?$atts["qr-code"]:null,
            "blockie-owner"             => WPSC_helpers::valArrElement($atts, 'blockie-owner')?$atts["blockie-owner"]:null,
            "owner-text"                => WPSC_helpers::valArrElement($atts, 'owner-text')?$atts["owner-text"]:null,
            "owner"                     => WPSC_helpers::valArrElement($atts, 'owner')?$atts["owner"]:null,
            "etherscan"                 => WPSC_helpers::valArrElement($atts, 'etherscan')?$atts["etherscan"]:null,
            "owner-short"               => WPSC_helpers::valArrElement($atts, 'owner-short')?$atts["owner-short"]:null,
            "txid"                      => WPSC_helpers::valArrElement($atts, 'txid')?$atts["txid"]:null,
            "genesis"                   => __('Genesis', 'wp-smart-contracts'),
            "txid-short"                => WPSC_helpers::valArrElement($atts, 'txid-short')?$atts["txid-short"]:null
        ];

        $atts_coin_view_wallet = [
            "xdai" => $xdai,
            "wallet" => __('Wallet', 'wp-smart-contracts'),
            "wallet-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/wallet.svg',
            "wallet-icon-white" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/wallet-white.svg',
            "balance" => __('Balance', 'wp-smart-contracts'),
            "the-balance" => '', // $m->render(WPSC_Mustache::getTemplate('coin-view-block-explorer-balance'), []),
            "balance-tooltip" => __('Check the balance of specific accounts', 'wp-smart-contracts'),
            "transfer" => __('Transfer', 'wp-smart-contracts'),
            "transfer-tooltip" => __('Transfer an amount of tokens from your account to another', 'wp-smart-contracts'),
            "transfer-from" => __('Transfer from', 'wp-smart-contracts'),
            "transfer-from-tooltip" => __('Expend tokens previously approved from an account', 'wp-smart-contracts'),
            "approve" => __('Approve', 'wp-smart-contracts'),
            "approve-tooltip" => __('Authorize an account to withdraw your tokens up to a specified amount', 'wp-smart-contracts'),
            "burn" => __('Burn', 'wp-smart-contracts'),
            "burn-tooltip" => __('Destroy (burn) an specific amount of tokens from your account', 'wp-smart-contracts'),
            "burn-from" => __('Burn from', 'wp-smart-contracts'),
            "burn-from-tooltip" => __('Burn tokens previously approved from an account', 'wp-smart-contracts'),
            "mint" => __('Mint', 'wp-smart-contracts'),
            "mint-tooltip" => __('Create new tokens and assign them to an account', 'wp-smart-contracts'),
            'add-minter' => __('Add Minter Role', 'wp-smart-contracts'),
            'tooltip-minter' => __('Allow this account to create tokens', 'wp-smart-contracts'),
            'add-pauser' => __('Add Pauser Role', 'wp-smart-contracts'),
            'tooltip-pauser' => __('Allow this account to pause all activity in this contract', 'wp-smart-contracts'),
            "pause" => __('Pause', 'wp-smart-contracts'),
            "pause-tooltip" => __('Pause token activity', 'wp-smart-contracts'),
            "resume" => __('Resume', 'wp-smart-contracts'),
            "resume-tooltip" => __('Resume token activity', 'wp-smart-contracts'),
            "address-from" => __('From address', 'wp-smart-contracts'),
            "address-to" => __('To address', 'wp-smart-contracts'),
            "amount" => __('Amount', 'wp-smart-contracts'),
            "scan" => __('Scan', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "flavor" => $wpsc_flavor,
            "cancel" => __('Cancel', 'wp-smart-contracts'),
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "renounce-pauser" => __('Renounce Pauser Role', 'wp-smart-contracts'),
            'tooltip-renounce-pauser' => __('Remove the pauser Role from your account', 'wp-smart-contracts'),
            "renounce-minter" => __('Renounce Minter Role', 'wp-smart-contracts'),
            'tooltip-renounce-minter' => __('Remove the minter Role from your account', 'wp-smart-contracts'),
        ];

        $atts_dex = [
            "dex" => __('Exchanges', 'wp-smart-contracts'),
            "exchange" => __('Exchange', 'wp-smart-contracts'),
            "wpsc_forkdelta" => $wpsc_forkdelta,
            "wpsc_uniswap" => $wpsc_uniswap,
            "forkdelta" => "https://forkdelta.app/#!/trade/" . $wpsc_contract_address . "-ETH",
            "forkdelta-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/forkdelta.png',
            "forkdelta-domain" => 'ForkDelta.app',
            "uniswap" => "https://app.uniswap.org/#/swap?outputCurrency=" . $wpsc_contract_address,
            "uniswap-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/uniswap.png',
            "uniswap-domain" => 'Uniswap.org',
            "etherdelta" => "https://etherdelta.com/#" . $wpsc_contract_address . "-ETH",
            "etherdelta-logo" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/etherdelta.png',
            "etherdelta-domain" => 'EtherDelta.com',
            "token-symbol" => $the_token_symbol,
        ];


        if ($wpsc_flavor == "chocolate") {
            $atts_coin_view_wallet["is_chocolate"] = true;
        }

        if ($wpsc_txid) {
            $atts_coin_view_addresses["txid-exists"] = true;
        }

        if ($wpsc_contract_address) {
            $atts_coin_view_wallet["contract-exists"] = true;
            $atts_coin_view_token["contract-exists"] = true;
            $atts_coin_view_addresses["contract-exists"] = true;
            $block_explorer_atts["contract-exists"] = true;
            $atts_dex["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $atts["contract-address"] . "</p>" .
                        "<p>The URL of your block explorer is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('coin-view'), [

            'msg-box' => $msg_box,

            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('You are not connected', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
                    ]
                ),

            'coin-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-brand'), 
                    [
                        'title' => $wpsc_title,
                        'social-networks' => $social_networks,
                        'content' => $wpsc_content,
                        'thumbnail' => $wpsc_thumbnail
                    ]
                ),

            'coin-view-token' => (WPSC_helpers::valArrElement($params, 'hide-token') and $params['hide-token'] and $params['hide-token']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-token'), 
                    $atts_coin_view_token
                ),
            
            'coin-view-addresses' => (WPSC_helpers::valArrElement($params, 'hide-address') and $params['hide-address'] and $params['hide-address']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-addresses'), 
                    $atts_coin_view_addresses
                ),
            
            'coin-view-wallet' => (WPSC_helpers::valArrElement($params, 'hide-wallet') and $params['hide-wallet'] and $params['hide-wallet']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-wallet'),
                    $atts_coin_view_wallet
                ),
            
            'coin-view-dex' => (!$wpsc_forkdelta and !$wpsc_uniswap)?'':
                $m->render(
                  WPSC_Mustache::getTemplate('coin-view-dex'),
                  $atts_dex
                ),

            'coin-view-block-explorer' =>  (WPSC_helpers::valArrElement($params, 'hide-block') and $params['hide-block'] and $params['hide-block']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('coin-view-block-explorer'), 
                    $block_explorer_atts
                ),

            'coin-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('coin-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    public function crowdfunding($params) {

        $the_id = self::getPostID($params);

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

        $m = new Mustache_Engine;

        // initialization
        $wpsc_flavor        = null;
        $wpsc_minimum       = null;
        $wpsc_approvers     = null;
        $atts = [];

        // show contract
        if ($wpsc_contract_address) {

            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
            $wpsc_minimum       = get_post_meta($the_id, 'wpsc_minimum', true);
            $wpsc_approvers     = get_post_meta($the_id, 'wpsc_approvers', true);

            $crowdInfo = [
                "type" => $wpsc_flavor,
                "factor" => $wpsc_approvers,
                "minimum" => $wpsc_minimum,
                "size" => "mini",
                "approvers_label" => __("Approvers Percentage", "wp-smart-contracts"),
                "minimum_label" => __("Minimum", "wp-smart-contracts")
            ];
            if ($wpsc_flavor=="mango") $crowdInfo["color"] = "orange";
            if ($wpsc_flavor=="bluemoon") $crowdInfo["color"] = "teal";
            if ($wpsc_flavor=="bubblegum") $crowdInfo["color"] = "purple";
            $crowdInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

            $atts = [
                'ethereum-network' => $network_val,
                'ethereum-color' => $color,
                'ethereum-icon' => $icon,
                'contract-address' => $wpsc_contract_address,
                'etherscan' => $etherscan,
                'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
                'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
                'owner-text' => __('Owner Account', 'wp-smart-contracts'),
                'qr-code' => $wpsc_qr_code,
                'blockie' => $wpsc_blockie,
                'blockie-owner' => $wpsc_blockie_owner,
                'crowd-info' => $m->render(WPSC_Mustache::getTemplate('crowdfunding-info'), $crowdInfo),
                'crowd-logo' => get_the_post_thumbnail_url($the_id),
                'contract-address-short' => WPSC_helpers::shortify($wpsc_contract_address),
                'txid' => $wpsc_txid,
                'txid-short' => WPSC_helpers::shortify($wpsc_txid),
                'owner' => $wpsc_owner,
                'owner-short' => WPSC_helpers::shortify($wpsc_owner)
            ];

            if ($wpsc_txid) {
                $atts["txid-exists"] = true;
            }

        }

        $atts_crowd_view_contract = [
            'blockie' => WPSC_helpers::valArrElement($atts, 'blockie')?$atts["blockie"]:null,
            'contract-name' => __('Crowdfunding', 'wp-smart-contracts'),
            'color' => $color,
            'icon' => $icon,
            'ethereum-network' => WPSC_helpers::valArrElement($atts, 'ethereum-network')?$atts["ethereum-network"]:null,
            'crowd-info' => WPSC_helpers::valArrElement($atts, 'crowd-info')?$atts["crowd-info"]:null,
            'title' => $wpsc_title,
            'content' => $wpsc_content,
            'thumbnail' => $wpsc_thumbnail
        ];

        $atts_crowd_view_panel = [
            'network' => $wpsc_network,
            'minimum' => $wpsc_minimum,
            'minimum-contribution' => __('Minimum contribution', 'wp-smart-contracts'),
            'panel' => __('Contributions', 'wp-smart-contracts'),
            'requests' => __('Requests', 'wp-smart-contracts'),
            'balance' => __('Balance', 'wp-smart-contracts'),
            'contribute' => __('Contribute', 'wp-smart-contracts'),
            'contribute-tooltip' => __('Amount to donate to the campaign', 'wp-smart-contracts'),
            'send' => __('Send', 'wp-smart-contracts'),
            'cancel' => __('Cancel', 'wp-smart-contracts'),
            'amount' => __('Amount', 'wp-smart-contracts'),
            'contributors' => __('Contributors', 'wp-smart-contracts'),
            'approve' => __('Approve', 'wp-smart-contracts'),
            'create-request' => __('Create Request', 'wp-smart-contracts'),
            'request' => __('Create request', 'wp-smart-contracts'),
            'description' => __('Add a description', 'wp-smart-contracts'),
            'create-request-tooltip' => __('A request to withdraw funds from the contract. Requests must be approved by approvers', 'wp-smart-contracts'),
            'finalize-request' => __('Finalize Request', 'wp-smart-contracts'),
            'scan' => __('Scan', 'wp-smart-contracts'),
            'address-to' => __('Destination address', 'wp-smart-contracts'),
            'wpsc-contract-address' => $wpsc_contract_address,
            'wpsc-flavor' => $wpsc_flavor,
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "check-contribution" => __('Check contribution', 'wp-smart-contracts'),
        ];

        if ($wpsc_txid) {
            $atts_crowd_view_addresses["txid-exists"] = true;
        }

        if ($wpsc_contract_address) {
            $atts_crowd_view_contract["contract-exists"] = true;
            $atts_crowd_view_addresses["contract-exists"] = true;
            $atts_crowd_view_panel["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $atts["contract-address"] . "</p>" .
                        "<p>The URL of your Crowdfunding is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('crowd-view'), [

            'msg-box' => $msg_box,
            
            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('You are not connected', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts'),
                    ]
                ),
            
            'crowd-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-brand'), 
                    $atts_crowd_view_contract
                ),
            
            'crowd-view-panel' => (WPSC_helpers::valArrElement($params, 'hide-panel') and $params['hide-panel'] and $params['hide-panel']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-panel'), 
                    $atts_crowd_view_panel
                ),

            'crowd-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('crowd-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    // return a timestamp using UTC time
    static public function utc_timestamp($input) {
        $utc_time_zone = new DateTimeZone("UTC");
        $date = new DateTime( $input, $utc_time_zone );            
        return $date->format('U');
    }

    public function ico($params) {

        $the_id = self::getPostID($params);

        $wpsc_thumbnail = get_the_post_thumbnail_url($the_id);
        $wpsc_title = get_the_title($the_id);
        $wpsc_content = get_post_field('post_content', $the_id);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        $wpsc_flavor        = null;

        $native_coin = WPSC_helpers::nativeCoinName($wpsc_network);

        // show contract
        if ($wpsc_contract_address) {
            $wpsc_flavor        = get_post_meta($the_id, 'wpsc_flavor', true);
        }

        $timed = get_post_meta($the_id, 'wpsc_adv_timed', true);

        if ($timed==="false") $timed=false;

        $wpsc_hardcap = get_post_meta($the_id, 'wpsc_adv_hard', true);
        if ($wpsc_hardcap==="false") $wpsc_hardcap=false;

        if ($timed) {

            $utc_now = gmdate('Y-m-d');

            $now = self::utc_timestamp($utc_now);

            $opening_string = get_post_meta($the_id, 'wpsc_adv_opening', true) . " 00:00:00";
            $closing_string = get_post_meta($the_id, 'wpsc_adv_closing', true) . " 23:59:59";

            $opening = self::utc_timestamp($opening_string);
            $closing = self::utc_timestamp($closing_string);

            $opening_human = date("F j, Y, g:i a", $opening) . " GMT";
            $closing_human = date("F j, Y, g:i a", $closing) . " GMT";

            $opening_human_short = date("M j, Y", $opening) . " GMT";
            $closing_human_short = date("M j, Y", $closing) . " GMT";

            if ($now>=$opening and $now<=$closing) {
                $is_open = true;
                // if timed can contribute only if open
                $can_contribute = true;
            }
            if ($utc_now>$closing_string) {
                $is_closed = true;
            }

            // if timed and open or closed show how much is raised
            if ((isset($is_open) and $is_open) or (isset($is_closed) and $is_closed)) {
                $show_raised = true;
            }

        } else {
            // if not timed can contribute any time and always shows raised
            $can_contribute = true;
            $show_raised = true;
        }

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo(get_post_meta($the_id, 'wpsc_network', true));

        $is_bubblegum = false;
        if ($wpsc_flavor=="bubblegum") {
            $is_bubblegum = true;
        }

        $m = new Mustache_Engine;

        $atts_ico_view_brand = [
            "wpsc_thumbnail" => $wpsc_thumbnail,
            "wpsc_title" => $wpsc_title,
            "wpsc_content" => $wpsc_content,
            "etherscan" => $etherscan,
            "color" => $color,
            "icon" => $icon,
            "network_val" => $network_val,
            "token-name" => __('Token name', 'wp-smart-contracts'),
            "token-symbol" => __('Token Symbol', 'wp-smart-contracts'),
            "initial-supply" => __('Initial supply', 'wp-smart-contracts'),
            "hard-cap" => __('Hard cap', 'wp-smart-contracts'),
            "rate" => __('Rate', 'wp-smart-contracts'),
            "calendar" => __('Calendar', 'wp-smart-contracts'),
            "ico-begins" => __('ICO Begins', 'wp-smart-contracts'),
            "open-until" => __('Open until', 'wp-smart-contracts'),
            "days" => __('days', 'wp-smart-contracts'),
            "hrs" => __('hrs', 'wp-smart-contracts'),
            "whitelist" => __('Whitelist', 'wp-smart-contracts'),
            "whitelist-desc" => __('Only whitelisted users can contribute', 'wp-smart-contracts'),
            "min" => __('min', 'wp-smart-contracts'),
            "sec" => __('sec', 'wp-smart-contracts'),
            "raised" => __('Raised', 'wp-smart-contracts'),
            "sold" => __('sold!', 'wp-smart-contracts'),
            "hard-cap-reached" => __('Hardcap reached', 'wp-smart-contracts'),
            "send-ether" => __('Contribute by transfering Ether', 'wp-smart-contracts'),
            "send-ether-address" => __('Send contributions directly to the Contract', 'wp-smart-contracts'),
            "copied" => __('Copied!', 'wp-smart-contracts'),
            "erc20-wallet" => __('You will receive your tokens in the same address you use to send Ether contribution. Please make sure you are using an ERC20 Token compatible wallet.', 'wp-smart-contracts'),
            "no-exchange" => __('Do not send contributions from an exchange', 'wp-smart-contracts'),
            "buy-tokens" => __('Buy Tokens', 'wp-smart-contracts'),
            "buy" => __('Buy', 'wp-smart-contracts'),
            "browser-wallet" => __('Using Metamask', 'wp-smart-contracts'),
            "contribute" => __('Contribute', 'wp-smart-contracts'),
            "text_4" => __('You can receive your tokens in a different Ethereum account', 'wp-smart-contracts'),
            "ico-icon" => plugins_url( "assets/img/ico.png", dirname(__FILE__) ),

            "wpsc-add-token-to-metamask" => $m->render(
                WPSC_Mustache::getTemplate('add-token-to-metamask'), [
                    "network" => $network_val,
                    "contract-address" => get_post_meta($the_id, 'wpsc_token_contract_address', true),
                    "token-symbol" => strtoupper(get_post_meta($the_id, 'wpsc_coin_symbol', true)),
                    "fox" => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) )
                ]
            ),

            "wpsc_adv_hard" => $wpsc_hardcap,
            "wpsc_adv_cap" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_adv_cap', true)),
            "wpsc_adv_white" => get_post_meta($the_id, 'wpsc_adv_white', true),
            "wpsc_adv_pause" => get_post_meta($the_id, 'wpsc_adv_pause', true),
            "wpsc_adv_timed" => $timed,
            "wpsc_adv_opening" => isset($opening)?$opening:null,
            "wpsc_adv_closing" => isset($closing)?$closing:null,
            "wpsc_coin_name" => get_post_meta($the_id, 'wpsc_coin_name', true),
            "wpsc_coin_symbol" => strtoupper(get_post_meta($the_id, 'wpsc_coin_symbol', true)),
            "wpsc_total_supply" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_total_supply', true)),
            "wpsc_rate" => WPSC_helpers::formatNumber(get_post_meta($the_id, 'wpsc_rate', true)),
            "wpsc_native_coin" => $native_coin,
            "timed" => __('Timed', 'wp-smart-contracts'),
            "from" => __('From', 'wp-smart-contracts'),
            "to" => __('to', 'wp-smart-contracts'),
            "token-address" => __('Token Address', 'wp-smart-contracts'),
            "or" => __('OR', 'wp-smart-contracts'),
            "wpsc_contract_address" => $wpsc_contract_address,
            "wpsc_contract_address_short" => WPSC_helpers::shortify($wpsc_contract_address, true),
            "wpsc_blockie" => get_post_meta($the_id, 'wpsc_blockie', true),
            "wpsc_blockie_token" => get_post_meta($the_id, 'wpsc_blockie_token', true),
            "wpsc_token_contract_address" => get_post_meta($the_id, 'wpsc_token_contract_address', true),
            "wpsc_token_contract_address_short" => WPSC_helpers::shortify(get_post_meta($the_id, 'wpsc_token_contract_address', true), true),
            "wpsc_qr_code" => get_post_meta($the_id, 'wpsc_qr_code', true),
            "wpsc_token_qr_code" => get_post_meta($the_id, 'wpsc_token_qr_code', true),
            "opening_human_short" => isset($opening_human_short)?$opening_human_short:null,
            "closing_human_short" => isset($closing_human_short)?$closing_human_short:null,
            "block-explorer" => __('Block Explorer', 'wp-smart-contracts'),
            "block-explorer-link" => get_permalink(get_post_meta($the_id, 'token_id', true)),

            "resume" => __('Resume', 'wp-smart-contracts'),
            "pause" => __('Pause', 'wp-smart-contracts'),
            "cancel" => __('Cancel', 'wp-smart-contracts'),

            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            "is_bubblegum" => $is_bubblegum,
            "tokens-to-sell" => __('Total tokens to sell', 'wp-smart-contracts'),

        ];

        $atts_ico_view_panel = [

            "is_open" => isset($is_open)?$is_open:null,
            "is_closed" => isset($is_closed)?$is_closed:null,
            "can_contribute" => isset($can_contribute)?$can_contribute:null,
            "show_raised" => isset($show_raised)?$show_raised:null,
            "opening_human" => isset($opening_human)?$opening_human:null,
            "closing_human" => isset($closing_human)?$closing_human:null,
            "opening" => isset($opening)?$opening * 1000:null,
            "closing" => isset($closing)?$closing * 1000:null,

            'network' => $wpsc_network,
            'wpsc-contract-address' => $wpsc_contract_address,
            'wpsc-flavor' => $wpsc_flavor,
    
            "ico-will-open" => __('ICO Coming Soon', 'wp-smart-contracts'),
            "ico-is-open" => __('ICO Is Open', 'wp-smart-contracts'),
            "ico-is-closed" => __('ICO Is Closed', 'wp-smart-contracts'),
            "ico-closed" => __('Closed on', 'wp-smart-contracts'),

            "ico-contribute" => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/ico.png',
            "contribute-tooltip" => __('Purchase tokens directly from ICO contract.', 'wp-smart-contracts'),
            "contribute-help" => __('This is the address where you are going to receive the tokens. The beneficiary account has to be a valid ERC20 token compatible address.', 'wp-smart-contracts'),
            "what-is" => __('What\'s this?', 'wp-smart-contracts'),
            "amount-ether" => __('Amount to spend', 'wp-smart-contracts'),
            "send" => __('Send', 'wp-smart-contracts'),
            "cancel" => __('Cancel', 'wp-smart-contracts'),
            "scan" => __('Scan', 'wp-smart-contracts'),
            "beneficiary" => __('Beneficiary account', 'wp-smart-contracts'),
            "tx-in-progress" => __('Transaction in progress', 'wp-smart-contracts'),
            "deploy-icon" => plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif',
            "click-confirm" => __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts'),
            "please-patience" => __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts'),
            
        ] + $atts_ico_view_brand;

        if ($wpsc_contract_address) {
            $atts_ico_view_panel["contract-exists"] = true;
        }

        $atts_source_code = WPSC_Metabox::wpscGetMetaSourceCodeAtts($the_id);

        $msg_box = "";

        if (WPSC_helpers::valArrElement($_GET, 'welcome')) {
            $actual_link = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');
            $msg_box = $m->render(
                WPSC_Mustache::getTemplate('msg-box'), 
                [
                    'type' => 'info',
                    'icon' => 'info',
                    'title' => __('Important Information', 'wp-smart-contracts'),
                    'msg' => "<p>Your contract was successfully deployed to the address: " . $wpsc_contract_address . "</p>" .
                        "<p>The URL of your ICO is: " . $actual_link . "</p>" .
                        "<p>Please store this information for future reference.</p>"
                    ]
                );
        }

        return $m->render(WPSC_Mustache::getTemplate('ico-view'), [
    
            'msg-box' => $msg_box,

            'view-metamask' => 
                $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('You are not connected', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts'),
                    ]
                ),

            'ico-view-brand' => (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('ico-view-brand'), 
                    $atts_ico_view_brand
                ),

            'ico-view-panel' => (WPSC_helpers::valArrElement($params, 'hide-panel') and $params['hide-panel'] and $params['hide-panel']=="true")?'':
                $m->render(
                    WPSC_Mustache::getTemplate('ico-view-panel'), 
                    $atts_ico_view_panel
                ),

            'ico-view-audit' =>   (WPSC_helpers::valArrElement($params, 'hide-audit') and $params['hide-audit'] and $params['hide-audit']=="true")?'':
                $m->render(
                  WPSC_Mustache::getTemplate('crowd-view-audit'),
                  $atts_source_code
                ),

        ]);

    }

    static private function drawCollections() {

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $const = "THEIDGOESHERE";

        $link_with_arg = str_replace($const, "", add_query_arg("id", $const, $actual_link));

        $posts = get_posts([
            'post_type' => 'nft-collection',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);
        foreach($posts as $i=>$p) {
            $posts[$i]->img=get_the_post_thumbnail_url($p->ID);
            $posts[$i]->permalink=get_permalink($p->ID);
        }
        $m = new Mustache_Engine;
        return $m->render(WPSC_Mustache::getTemplate('nft-collections'), [
            "cards"=>$posts, 
            'nft-view-menu' => self::getViewMenu($m, 0, null),
            "link_with_arg" => $link_with_arg
        ]);
    }

    static public function validateNFTFE($collection_id, $user, $nft_id=0) {

		if (!$user->ID) return "Invalid user";

		if (!$collection_id) return "Invalid collection ID";
		
        if ($nft_id) {
            $wpsc_anyone_can_author = get_post_meta($collection_id, "wpsc_anyone_can_author", true);
            if ($wpsc_anyone_can_author!="on") {
                $author_id = get_post_field ('post_author', $nft_id);
                if ($user->ID != $author_id) return "You are not authorized to edit this NFT";    
            }
            $post_type = get_post_field ('post_type', $nft_id);
            if ($post_type != "nft") return "Unexpected error, wrong post type.";
            $wpsc_item_collection = get_post_field ('wpsc_item_collection', $nft_id);
            if ($wpsc_item_collection != $collection_id) return "Unexpected error, invalid collection id or nft id.";
            
        }
		
		if (get_post_type($collection_id)!="nft-collection") return "Invalid collection ID";
	
		$wpsc_nft_roles = get_post_meta($collection_id, "wpsc_nft_roles", true);
	
		if (empty(array_intersect($wpsc_nft_roles, $user->roles))) return "Unauthorized user";

		return false;
	
	}

    public function nftAuthor($params) {

        $collection_id = false;
        $author_id = false;

        if (WPSC_helpers::valArrElement($_GET, 'id')) {
            $collection_id = (int) $_GET["id"];
        }
        if (WPSC_helpers::valArrElement($_GET, 'a')) {
            $author_id = (int) $_GET["a"];
        }

        if (!$collection_id) return self::drawCollections();
        if (!$author_id) return "Invalid Author ID";

        $collection = get_post($collection_id);

        $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

        $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
        $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
        $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
        
        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['collection-id'] = $collection_id;
        $data['collection-name'] = get_the_title($collection_id);
        $data["collection-link"] = get_permalink($collection_id);

        $m = new Mustache_Engine;

        $data['nft-view-menu'] = self::getViewMenu($m, $collection_id, null);

        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($collection_id, 'wpsc_network', true));

        $data['wpsc-nft-option'] = "collection";
        $nft_ids = WPSC_Queries::getNFTIdsByAuthor($collection_id, $author_id);

        $user_data = get_userdata($author_id);
        $author["avatar"] = get_avatar_url($author_id);
        $author["description"] = get_the_author_meta("description", $author_id);
        $data['breadcrumb-level2'] = $author["display_name"] = $user_data->display_name;
        $author["user_url"] = $user_data->user_url;
        $author["user_registered"] = date("F jS, Y", strtotime($user_data->user_registered));
        
        $data["custom-title"] = $m->render(WPSC_Mustache::getTemplate('nft-author'), $author);

        if (!empty($nft_ids)) {
            $data['wpsc-nft-params-nft-ids'] = json_encode(array_column($nft_ids, "nft_id"));
        }

        $data["nft-items-per-page"] = WPSCSettingsPage::nftItemsPerPage();

        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );

        return $m->render(WPSC_Mustache::getTemplate('nft-my-items'), $data);

    }

    public function nftMyBids($params) {

        $collection_id = (int) $_GET["id"];

        if (!$collection_id) return self::drawCollections();
		if (get_post_type($collection_id)!="nft-collection") self::drawCollections();

        $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

        $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
        $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
        $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
        
        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['collection-id'] = $collection_id;
        $data['collection-name'] = get_the_title($collection_id);
        $data["collection-link"] = get_permalink($collection_id);

        $m = new Mustache_Engine;

        $data['nft-view-menu'] = self::getViewMenu($m, $collection_id, "my-bids");

        $data['page-title'] = "My Bids";
        $data['wpsc-nft-option'] = "my-bids";
        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($collection_id, 'wpsc_network', true));

        $data["nft-items-per-page"] = WPSCSettingsPage::nftItemsPerPage();

        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );
        
        $nft_ids = WPSC_Queries::getNFTIds($collection_id);
        if (!empty($nft_ids)) {
            $data['wpsc-nft-params-nft-ids'] = json_encode(array_column($nft_ids, "nft_id"));
        }

        return $m->render(WPSC_Mustache::getTemplate('nft-my-items'), $data);

    }


    public function nftMyItems($params) {

        $collection_id = 0;
        if (WPSC_helpers::valArrElement($_GET, 'id')) {
            $collection_id = (int) $_GET["id"];
        }

        if (!$collection_id) return self::drawCollections();
		if (get_post_type($collection_id)!="nft-collection") return self::drawCollections();

        $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

        $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
        $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
        $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
        
        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['collection-id'] = $collection_id;
        $data['collection-name'] = get_the_title($collection_id);
        $data["collection-link"] = get_permalink($collection_id);

        $m = new Mustache_Engine;

        $data['nft-view-menu'] = self::getViewMenu($m, $collection_id, "my-items");

        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($collection_id, 'wpsc_network', true));

        $data['page-title'] = "My Items";
        $data['wpsc-nft-option'] = "my-items";

        $data["nft-items-per-page"] = WPSCSettingsPage::nftItemsPerPage();

        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );

        $nft_ids = WPSC_Queries::getNFTIds($collection_id);
        if (!empty($nft_ids)) {
            $data['wpsc-nft-params-nft-ids'] = json_encode(array_column($nft_ids, "nft_id"));
        }

        return $m->render(WPSC_Mustache::getTemplate('nft-my-items'), $data);
    }

    public function nftMint($params) {

        $collection_id = $nft_id = null;
        
        if (WPSC_helpers::valArrElement($_GET, 'id')) {
            $collection_id = (int) $_GET["id"];
        }
        if (WPSC_helpers::valArrElement($_GET, 'nft_id')) {
            $nft_id = (int) $_GET["nft_id"];
        }

        if (!$collection_id) return self::drawCollections();

        $taxs = self::getTaxonomy('nft-taxonomy', $nft_id);
        $tags = self::getTaxonomy('nft-tag', $nft_id);

        $m = new Mustache_Engine;
        if ( is_user_logged_in() ) {
            if ($error = self::validateNFTFE($collection_id, wp_get_current_user(), $nft_id)) {
                return $m->render(WPSC_Mustache::getTemplate('wpsc-message'), ["error"=>true, "title"=>"An error has ocurred", "message"=>$error]);
            } else {

                $data = [
                    "taxs"=>$taxs,
                    "tags"=>$tags, 
                    "collection-id"=>$collection_id, 
                    "nft-id"=>$nft_id
                ];

                if ($nft_id) {

                    $nft_item = get_post($nft_id);

                    if ($nft_item->post_type=="nft") {

                        $data["post_content"]    = $nft_item->post_content;
                        $data["post_title"]      = $nft_item->post_title;
                        $data["wpsc_nft_owner"]  = get_post_meta($nft_id, "wpsc_nft_owner", true);

                        $wpsc_media_type = get_post_meta($nft_id, "wpsc_media_type", true);
                        switch($wpsc_media_type) {
                        case "image":
                            $data["wpsc_media_type_image"] = true;
                            break;
                        case "video":
                            $data["wpsc_media_type_video"] = true;
                            break;
                        case "audio":
                            $data["wpsc_media_type_audio"] = true;
                            break;
                        case "document":
                            $data["wpsc_media_type_document"] = true;
                            break;
                        }

                        $data["wpsc_nft_media_json"] = get_post_meta($nft_id, "wpsc_nft_media_json", true);

                    }
            
                }

                $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

                $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
                $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
                $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
                $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
                
                $data['text'] = __('You are not connected', 'wp-smart-contracts');
                $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
                $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');

                $data["mint-icon"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/creature.png';
                $data["mint"] = __("Mint", "wp-smart-contracts");
                $data["mint-tooltip"] = __("Create the NFT Item on the Blockchain", "wp-smart-contracts");
                $data["scan"] = __("Scan", "wp-smart-contracts");
                $data["address-to"] = __("Beneficiary address", "wp-smart-contracts");
                $data["cancel"] = __("Cancel", "wp-smart-contracts");
                $data["click-confirm"] = __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts');
                $data["please-patience"] = __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts');
                $data["tx-in-progress"] = __('Transaction in progress', 'wp-smart-contracts');
                $data["deploy-icon"] = plugin_dir_url( dirname(__FILE__) ) . '/assets/img/animated.gif';
                
                $data["nft-view-menu"] = self::getViewMenu($m, $collection_id, "mint");
                $data["collection-name"] = get_the_title($collection_id);
                $data["collection-link"] = get_permalink($collection_id);

                $data['view-metamask'] = $m->render(
                    WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                    [
                        'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                        'text' => __('You are not connected', 'wp-smart-contracts'),
                        'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
                    ]
                );
    
                return $m->render(WPSC_Mustache::getTemplate('nft-collection-mint'), $data);
            }                
        } else {
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return $m->render(WPSC_Mustache::getTemplate('wpsc-message'), ["title"=>"Only registered users", "message"=>__("This option is available only for logged-in users, please <a href=\"".wp_login_url($actual_link)."\">login here</a>")]);
        }

    }

    private function integerify($arr_obj) {
        $ret = null;
        if (is_array($arr_obj)) {
            foreach($arr_obj as $obj) {
                $ret[] = $obj->term_id;
            }
        }
        return $ret;
    }

    private function getTaxonomy($taxonomy_slug, $nft_id) {
        $taxs = WPSC_Queries::getTaxonomy($taxonomy_slug);
        $tax_terms = self::integerify(wp_get_object_terms($nft_id, $taxonomy_slug));
        foreach ($taxs as $tax_id => $tax) {
            if (!empty($tax_terms) and array_search($tax["term_id"], $tax_terms)!==false) {
                $taxs[$tax_id]["selected"]=true;
            }
        }
        return $taxs;
    }
    
    public function nftTaxonomy($params) {

        $tax_id = get_queried_object()->term_id;

        $collection_id = (int) $_GET['id'];

        if (!$collection_id) return self::drawCollections();
        if (!$tax_id) return "Invalid Taxonomy ID";

        $collection = get_post($collection_id);

        $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

        $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
        $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
        $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
        
        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['collection-id'] = $collection_id;
        $data['collection-name'] = get_the_title($collection_id);
        $data["collection-link"] = get_permalink($collection_id);

        $m = new Mustache_Engine;

        $data['nft-view-menu'] = self::getViewMenu($m, $collection_id, null);

        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($collection_id, 'wpsc_network', true));

        $data['breadcrumb-level2'] = $data['page-title'] = $term_name = get_term( $tax_id )->name;
        $data['page-content'] = wpautop(apply_filters('the_content', $collection->post_content));
        $data['wpsc-nft-option'] = "collection";
        $nft_ids = WPSC_Queries::getNFTIdsByTaxonomy($collection_id, $tax_id);

        if (!empty($nft_ids)) {
            $data['wpsc-nft-params-nft-ids'] = json_encode(array_column($nft_ids, "nft_id"));
        }

        $data["nft-items-per-page"] = WPSCSettingsPage::nftItemsPerPage();

        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );

        return $m->render(WPSC_Mustache::getTemplate('nft-my-items'), $data);

    }

    public function nftCollection($params) {

        $collection_id = self::getPostID($params);

        if (!$collection_id) return self::drawCollections();
		if (get_post_type($collection_id)!="nft-collection") return self::drawCollections();

        $collection = get_post($collection_id);

        $data["wpsc_nft_token_uri"] = get_rest_url(null, 'wpsc/v1/nft/');

        $data["wpsc-contract-flavor"] = get_post_meta($collection_id, "wpsc_flavor", true);
        $data["wpsc-nft-marketplace-contract"] = get_post_meta($collection_id, "wpsc_contract_address", true);
        $data["wpsc-nft-network"] = get_post_meta($collection_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
        
        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['collection-id'] = $collection_id;
        $data['collection-name'] = get_the_title($collection_id);
        $data["collection-link"] = get_permalink($collection_id);

        $m = new Mustache_Engine;

        $data['nft-view-menu'] = self::getViewMenu($m, $collection_id, null);

        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($collection_id, 'wpsc_network', true));

        $data['page-title'] = $collection->post_title;
        $data['page-content'] = wpautop(apply_filters('the_content', $collection->post_content));
        $data['wpsc-nft-option'] = "collection";
        $nft_ids = WPSC_Queries::getNFTIds($collection_id);

        if (!empty($nft_ids)) {
            $data['wpsc-nft-params-nft-ids'] = json_encode(array_column($nft_ids, "nft_id"));
        }

        $data["nft-items-per-page"] = WPSCSettingsPage::nftItemsPerPage();

        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );

        return $m->render(WPSC_Mustache::getTemplate('nft-my-items'), $data);

    }

    public function staking($params) {

        $staking_id = self::getPostID($params);
    
        if (!$staking_id) return;
        if (get_post_type($staking_id)!="staking") return;
    
        $staking = get_post($staking_id);
    
        $data["wpsc-contract-flavor-beautified"] = ucfirst(get_post_meta($staking_id, "wpsc_flavor", true));
        $data["wpsc-contract-flavor"] = get_post_meta($staking_id, "wpsc_flavor", true);
        $data["wpsc-contract"] = get_post_meta($staking_id, "wpsc_contract_address", true);
        $data["wpsc-contract-short"] = WPSC_helpers::shortify($data["wpsc-contract"]);
        $data["blockie"] = get_post_meta($staking_id, 'wpsc_blockie', true);

        $data["wpsc-token"] = get_post_meta($staking_id, 'wpsc_token', true);
        $data["wpsc-token-short"] = WPSC_helpers::shortify($data["wpsc-token"]);
        $data["wpsc-symbol"] = get_post_meta($staking_id, 'wpsc_symbol', true);
        $data["wpsc-name"] = get_post_meta($staking_id, 'wpsc_name', true);
        $data["wpsc-decimals"] = get_post_meta($staking_id, 'wpsc_decimals', true);
        
        $data["wpsc_minimum"] = get_post_meta($staking_id, 'wpsc_minimum', true);
        $data["wpsc_penalty"] = get_post_meta($staking_id, 'wpsc_penalty', true);
        $data["wpsc_mst"] = get_post_meta($staking_id, 'wpsc_mst', true);
        $data["wpsc_apy"] = get_post_meta($staking_id, 'wpsc_apy', true);
        
        // almond variables
        $data["wpsc-token2"] = get_post_meta($staking_id, 'wpsc_token2', true);
        $data["wpsc-token-short2"] = WPSC_helpers::shortify($data["wpsc-token2"]);
        $data["wpsc-apy2"] = get_post_meta($staking_id, 'wpsc_apy2', true);    
        $data["wpsc-ratio1"] = get_post_meta($staking_id, 'wpsc_ratio1', true);    
        $data["wpsc-ratio2"] = get_post_meta($staking_id, 'wpsc_ratio2', true); 
        $data["wpsc-symbol2"] = get_post_meta($staking_id, 'wpsc_symbol2', true);
        $data["wpsc-name2"] = get_post_meta($staking_id, 'wpsc_name2', true);
        $data["wpsc-decimals2"] = get_post_meta($staking_id, 'wpsc_decimals2', true);
        if ($data["wpsc-contract-flavor"]=="almond") {
            $data["is-almond"]=true;
        }
           
        $data["wpsc-network"] = get_post_meta($staking_id, "wpsc_network", true);
        $data['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );

        list($data["color"], $data["icon"], $data["etherscan"], $data["network_val"]) = WPSC_Metabox::getNetworkInfo(get_post_meta($staking_id, 'wpsc_network', true));

        $data['text'] = __('You are not connected', 'wp-smart-contracts');
        $data['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');
        $data['text-wrong-net'] = __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts');
        $data['staking-id'] = $staking_id;
        $data["staking-link"] = get_permalink($staking_id);
        $data["cancel"] = __("Cancel", "wp-smart-contracts");
        $data["click-confirm"] = __('If you agree and wish to proceed, please click "CONFIRM" transaction in the Metamask Window, otherwise click "REJECT".', 'wp-smart-contracts');
        $data["please-patience"] = __('Please be patient. It can take several minutes. Don\'t close or reload this window.', 'wp-smart-contracts');
        $data["tx-in-progress"] = __('Transaction in progress', 'wp-smart-contracts');
        $data["deploy-icon"] = plugin_dir_url( dirname(__FILE__) ) . '/assets/img/deploy-identicon.gif';

        $data['page-title'] = $staking->post_title;
        $data['page-content'] = wpautop(apply_filters('the_content', $staking->post_content));
        $data['page-thumbnail'] = get_the_post_thumbnail_url($staking_id);

        $m = new Mustache_Engine;
    
        $data['view-metamask'] = $m->render(
            WPSC_Mustache::getTemplate('crowd-view-metamask'), 
            [
                'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                'text' => __('You are not connected', 'wp-smart-contracts'),
                'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
            ]
        );
    
        if (WPSC_helpers::valArrElement($params, 'hide-brand') and $params['hide-brand'] and $params['hide-brand']=="true") {
            $data["hide-brand"] = true;
        }
        if (WPSC_helpers::valArrElement($params, 'hide-stakes') and $params['hide-stakes'] and $params['hide-stakes']=="true") {
            $data["hide-stakes"] = true;
        }

        return $m->render(WPSC_Mustache::getTemplate('stake-view'), $data);
    
    }
    
    static private function getViewMenu($m, $wpsc_item_collection, $active, $edit_link=false) {
        $ret = [
            "my-items-link" => add_query_arg("id", $wpsc_item_collection, WPSC_assets::getNFTMyItemsPage()),
            "my-bids-link" => add_query_arg("id", $wpsc_item_collection, WPSC_assets::getNFTMyBidsPage()),
            $active => "true",
            "mint-link" => add_query_arg("id", $wpsc_item_collection, WPSC_assets::getNFTMintPage())
        ];

        if ($edit_link) {
            $ret["edit-link"] = add_query_arg(
                [
                    "id" => $wpsc_item_collection,
                    "nft_id" => get_the_ID()
                ], 
                WPSC_assets::getNFTMintPage()
            );
        }

        return $m->render(WPSC_Mustache::getTemplate('nft-view-menu'), $ret);

    }

    private static function getOrLoadTransient($the_id) {
        if ($the_id) {
            // generate transient using the endpoint
            wp_remote_get(get_rest_url(null, 'wpsc/v1/nft/'.$the_id));
            // return transient
            return get_transient("wpsc_nft_" . $the_id);
        }
    }

    public function nft($params) {

        $cats_terms = $tax_terms = null;

        $the_id = self::getPostID($params);

        $transient_data = self::getOrLoadTransient($the_id);
        
        $wpsc_title = (WPSC_helpers::valArrElement($transient_data, 'name'))?$transient_data["name"]:"";
        $wpsc_content = (WPSC_helpers::valArrElement($transient_data, 'description'))?$transient_data["description"]:"";
        $wpsc_item_collection = get_post_meta($the_id, "wpsc_item_collection", true);

        if (WPSC_helpers::valArrElement($transient_data, 'attributes') and is_array($transient_data["attributes"])) {
            foreach($transient_data["attributes"] as $att) {
                if (WPSC_helpers::valArrElement($att, 'trait_type') and strpos($att["trait_type"], "Category")===0 ) {
                    $cats_terms[] = $att;
                } elseif (empty(WPSC_helpers::valArrElement($att, 'trait_type'))) {
                    $tax_terms[] = $att;
                }
            }    
        }

        $original_author_id = (WPSC_helpers::valArrElement($transient_data, 'author_id'))?$transient_data["author_id"]:"";
        $original_author_avatar = (WPSC_helpers::valArrElement($transient_data, 'author_avatar'))?$transient_data["author_avatar"]:"";
        $wpsc_author_url = (WPSC_helpers::valArrElement($transient_data, 'author_external_url'))?$transient_data["author_external_url"]:"";

        $wpsc_collection_contract = get_post_meta($the_id, "wpsc_collection_contract", true);
        $wpsc_nft_id = get_post_meta($the_id, "wpsc_nft_id", true);
        $wpsc_nft_id_blockie = get_post_meta($the_id, "wpsc_nft_id_blockie", true);

        $wpsc_collection_title = get_the_title($wpsc_item_collection);
        $wpsc_collection_link = get_permalink($wpsc_item_collection);

        $wpsc_tag_bg_color = get_post_meta($wpsc_item_collection, "wpsc_tag_bg_color", true);
        $wpsc_tag_color = get_post_meta($wpsc_item_collection, "wpsc_tag_color", true);
        $wpsc_cat_bg_color = get_post_meta($wpsc_item_collection, "wpsc_cat_bg_color", true);
        $wpsc_cat_color = get_post_meta($wpsc_item_collection, "wpsc_cat_color", true);
        $wpsc_graph_line_color = get_post_meta($wpsc_item_collection, "wpsc_graph_line_color", true);
        $wpsc_graph_bg_color = get_post_meta($wpsc_item_collection, "wpsc_graph_bg_color", true);

        $wpsc_network = get_post_meta($the_id, 'wpsc_network', true);
        $wpsc_txid = get_post_meta($the_id, 'wpsc_txid', true);
        $wpsc_owner = get_post_meta($the_id, 'wpsc_owner', true);
        $wpsc_contract_address = get_post_meta($the_id, 'wpsc_contract_address', true);
        $wpsc_blockie = get_post_meta($the_id, 'wpsc_blockie', true);
        $wpsc_blockie_owner = get_post_meta($the_id, 'wpsc_blockie_owner', true);
        $wpsc_qr_code = get_post_meta($the_id, 'wpsc_qr_code', true);

        $wpsc_nft_media_json = get_post_meta($the_id, "wpsc_nft_media_json", true);
        $wpsc_media_type = get_post_meta($the_id, "wpsc_media_type", true);
        $wpsc_creator = get_post_meta($the_id, "wpsc_creator", true);
        $original_author = get_post_meta($the_id, "original_author", true);
        $wpsc_creator_blockie = get_post_meta($the_id, "wpsc_creator_blockie", true);

        $wpsc_blockie_collection = get_post_meta($wpsc_item_collection, 'wpsc_blockie', true);

        $wpsc_flavor = get_post_meta($wpsc_item_collection, 'wpsc_flavor', true);;

        $native_coin = WPSC_helpers::nativeCoinName($wpsc_network);

        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo(get_post_meta($the_id, 'wpsc_network', true));

        $is_bubblegum = false;
        if ($wpsc_flavor=="bubblegum") {
            $is_bubblegum = true;
        }
        $m = new Mustache_Engine;

        $show_edit_link = !self::validateNFTFE($wpsc_item_collection, wp_get_current_user(), $the_id);

        $wpsc_log_history = get_post_meta($the_id, "wpsc_log_history", true);

        $the_params = [
            'wpsc_nft_network' => $wpsc_network,
            'wpsc_contract_flavor' => $wpsc_flavor,
            'wpsc_nft_marketplace_contract' => $wpsc_collection_contract,
            'nft_token_id' => $wpsc_nft_id,
            "etherscan" => $etherscan,

            'view-metamask' => $m->render(
                WPSC_Mustache::getTemplate('crowd-view-metamask'), 
                [
                    'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
                    'text' => __('You are not connected', 'wp-smart-contracts'),
                    'text-wrong-net' => __('You are connected to a different network, or contract not deployed', 'wp-smart-contracts')
                ]
            ),
            "wpsc_show_header" => get_post_meta($wpsc_item_collection, "wpsc_show_header", true),
            'nft-view-menu' => self::getViewMenu($m, $wpsc_item_collection, "none", $show_edit_link),
            "collection-title"=>$wpsc_collection_title, 
            "collection-link"=>$wpsc_collection_link, 
            "nft-title"=>$wpsc_title,
            "collection-title"=>$wpsc_collection_title, 
            "collection-link"=>$wpsc_collection_link, 
            "nft-title"=>$wpsc_title,
            "cats"=>$cats_terms,
            "wpsc_cat_bg_color"=>$wpsc_cat_bg_color,
            "wpsc_cat_color"=>$wpsc_cat_color,
            "tags"=>$tax_terms, 
            "wpsc_tag_bg_color"=>$wpsc_tag_bg_color, 
            "wpsc_tag_color"=>$wpsc_tag_color,
            "wpsc-nft-media-json" => $wpsc_nft_media_json,
            "wpsc-media-type" => $wpsc_media_type, 
            "nft-title"=>$wpsc_title,
            "author" => $original_author,
            "author_link" => get_author_posts_url($original_author_id),
            "wpsc_creator" => $wpsc_creator,
            "wpsc_creator_short" => WPSC_helpers::shortify($wpsc_creator),
            "etherscan" => $etherscan,
            "content" => wpautop($wpsc_content),
            "original_author_avatar" => $original_author_avatar,
            "wpsc_creator_blockie" => $wpsc_creator_blockie,
            "wpsc_collection_contract" => $wpsc_collection_contract,
            "wpsc_collection_contract_short" => WPSC_helpers::shortify($wpsc_collection_contract),
            "wpsc_blockie_collection" => $wpsc_blockie_collection,
            "etherscan" => $etherscan,
            "color" => $color, 
            "icon" => $icon, 
            "network_val" => $network_val,
            "wpsc_nft_id" => $wpsc_nft_id,
            "wpsc_nft_id_blockie" => $wpsc_nft_id_blockie,
            'nft-transfer-progress' => $m->render(WPSC_Mustache::getTemplate('nft-transfer-progress'), [
                'animated-gif' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/animated.gif'
            ]),
            "wpsc_creator" => $wpsc_creator,
            "wpsc_creator_short" => WPSC_helpers::shortify($wpsc_creator),
            "wpsc_creator_link" => $etherscan . "address/" . $wpsc_creator,
            "wpsc_txid" => $wpsc_txid,
            "wpsc_txid_short" => WPSC_helpers::shortify($wpsc_txid),
            "wpsc_txid_link" => $etherscan . "tx/" . $wpsc_txid,
            "original_author" => $original_author,
            "original_author_id" => $original_author_id,
            "original_author_avatar" => $original_author_avatar,
            "wpsc_creator_blockie" => $wpsc_creator_blockie,
            "wpsc_author_url" => $wpsc_author_url,
            "wpsc_graph_bg_color" => $wpsc_graph_bg_color,
            "wpsc_graph_line_color" => $wpsc_graph_line_color,
            "endpoint" => get_rest_url(null, 'wpsc/v1/nft/') . $the_id,
            "wpsc_log_history" => $wpsc_log_history
        ];

        if ($wpsc_flavor=="suika") {
            $the_params["is-suika"]=true;
            $the_params["wpsc_flavor_color"]="red";
        } elseif ($wpsc_flavor=="mochi") {
            $the_params["wpsc_flavor_color"]="violet";
        } elseif ($wpsc_flavor=="matcha") {
            $the_params["wpsc_flavor_color"]="green";
        }
        
        $wpsc_nft_media_json_arr = json_decode($wpsc_nft_media_json, true);

        if ($media_mime = WPSC_IPFS_MEDIA::getMimeFromArr($wpsc_nft_media_json_arr)) {
            $the_params["media_files"] = WPSC_IPFS_MEDIA::getIpfsFromArr($wpsc_nft_media_json_arr);
            $the_params[$media_mime] = true;
            if ($media_mime=="doc") {
                $the_params["meter_clase_de_video"] = "documentClass";
            } elseif ($media_mime=="video") {
                $the_params["meter_clase_de_video"] = "videoClass";
            }
        }

        return $m->render(WPSC_Mustache::getTemplate('nft-view-all'), $the_params);

    }

    // return the post id from environment or from shortcode
    public static function getPostID($params) {

        $the_id = 0;

        if (is_array($params) and array_key_exists('id', $params) and $params["id"]) {
            $the_id = (int) $params['id'];
        }

        if (!$the_id) {
            $the_id = (int) get_the_ID();
        }

        return $the_id;

    }

}

