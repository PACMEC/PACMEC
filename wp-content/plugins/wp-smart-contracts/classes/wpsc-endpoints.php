<?php

if( ! defined( 'ABSPATH' ) ) die;

new WPSC_Endpoints();

/**
 * Handle etherscan api queries for block explorer view
 */

class WPSC_Endpoints {

    // prefix name for the transient variable
    const transientPrefix = 'wpsc_';

    const paginationOffset = 25;

    // define endpoints
    function __construct() {

        // get token supply
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/ping/', [
                'methods' => 'GET',
                'callback' => [ $this, 'ping' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // search transactions for one token and one account address
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx_contract_account/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<address>[a-zA-Z0-9-]+)/(?P<page>\d+)/(?P<internal>\d+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxAccountInContract' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // search all transactions for one token
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx_contract/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<page>\d+)/(?P<internal>\d+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxFromContract' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // get token supply
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/total_supply/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTotalSupply' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // get account balance
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/balance/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<address>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getBalance' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // get tx per id
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_tx/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)/(?P<txid>[a-zA-Z0-9-]+)/(?P<ignore_contract>[0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getTxId' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // get tx per id
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/remove_cache/', [
                'methods' => 'GET',
                'callback' => [ $this, 'removeCache' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // get code per contract
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get_code/(?P<network>\d+)/(?P<contract>[a-zA-Z0-9-]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'getCode' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // format numbers
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/format_float/(?P<float>[0-9\,\.e\+]+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'formatFloat' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // NFT endpoint
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/nft/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [ $this, 'nft' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/insert-nft/(?P<id>\d+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftInsert' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/nft-log/(?P<id>\d+)/(?P<txid>[a-zA-Z0-9-]+)/(?P<to>[a-zA-Z0-9-]+)/(?P<value>[0-9\.]+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftLog' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/save-deploy-nft/(?P<id>\d+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftSaveDeploy' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/get-nft-by-id/(?P<collid>\d+)/(?P<ids>[0-9-]+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftGetByIDs' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/is-minted/(?P<id>\d+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'isMinted' ],
                'permission_callback' => '__return_true'
            ]);
        });

        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/nft-ipfs-store', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftIPFSStore' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // given an attachment ID returns if it was uploaded to IPFS or not
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpsc/v1', '/nft-exists-ipfs/(?P<id>\d+)', [
                'methods' => 'POST',
                'callback' => [ $this, 'nftExistsIPFS' ],
                'permission_callback' => '__return_true'
            ]);
        });

        // if there is a change in a NFT clear transient endpoint response
        add_action('save_post', function ($post_id) {
            global $post; 
            if (!empty($post) and $post->post_type != 'nft'){
                return;
            }
            $transient_name = "wpsc_nft_" . $post_id;
            delete_transient($transient_name);
        });

    }

    // endpoint callbacks

    public static function ping($params) {
        return new WP_REST_Response(true);
    }

    public static function getTxAccountInContract($params) {
        
        check_ajax_referer('wp_rest', '_wpnonce');
        
        return new WP_REST_Response(
            self::getTx($params['network'], $params['contract'], $params['address'], $params['page'], $params['internal'])
        );
        
    }

    public static function getTxFromContract($params) {

        check_ajax_referer('wp_rest', '_wpnonce');
        
        return new WP_REST_Response(
            self::getTx($params['network'], $params['contract'], null, $params['page'], $params['internal'])
        );

    }

    static private function processInput($input) {

        if ($input) {

            $hashFunction = $type = $from = $to = $value = null;

            $hashFunction = substr($input, 0, 10);
            switch ($hashFunction) {
                case "0xa9059cbb":
                    $type="transfer";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 75, 63));
                    break;
                case "0x23b872dd":
                    $type="transferFrom";
                    $from = "0x" . substr($input, 34, 40);
                    $to = "0x" . substr($input, 98, 40);
                    $value = hexdec(substr($input, 138));
                    break;
                case "0x095ea7b3":
                    $type="approve";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x40c10f19":
                    $type="mint";
                    $to = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x42966c68":
                    $type="burn";
                    $value = hexdec(substr($input, 10));
                    break;
                case "0x79cc6790":
                    $type="burnFrom";
                    $from = "0x" . substr($input, 34, 40);
                    $value = hexdec(substr($input, 74));
                    break;
                case "0x8456cb59":
                    $type="pause";
                    break;
                case "0x3f4ba83a":
                    $type="resume";
                    break;
                case "0x983b2d56":
                    $type="addMinter";
                    break;
                case "0x82dc1ec4":
                    $type="addPauser";
                    break;
                case "0x98650275":
                    $type="renounceMinter";
                    break;
                case "0x6ef8d66d":
                    $type="renouncePauser";
                    break;
                case "0x80a70e5f": // raspberry
                case "0xe6c9f1f6": // raspberry wpst
                case "0x45c2e176": // bluemoon
                case "0x3e517ed1": // bluemoon wpst
                case "0x5b060530": // vanilla and pistachio
                case "0x558d4657": // chocolate
                case "0x37d325a1": // vanilla and pistachio wpst
                case "0x95d38e11": // chocolate wpst
                case "0x772d0f3c": // mango
                case "0xc19afa14": // mango wpst
                    $type="contractCreation";
                break;
                case "0xec8ac4d8":
                    $type="icoBuyTokens";
                    break;
                case "0x":
                    $type="icoDirectTransfer";
                    break;
            }
            if ($value) {
                // convert wei like units to ether like
                $value = $value / 1000000000000000000;
                $value = $value;
            }
            return [$hashFunction, $type, $from, $to, $value];
        } else {
            return [];
        }

    }

    // filter tx based on contract / account address
    private static function getTx($network, $contract, $address, $page, $internal) {

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        if (!$page) $page = 1;

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $contract, $address, $page, $internal)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            // filter by contract and account
            if ($address) {
                $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=tokentx&address=' . $address . 
                    '&sort=desc&page=' . $page . '&offset=' . self::paginationOffset . '&apikey=' . trim($etherscan_api_key["api_key"]) . 
                        '&contractAddress=' . $contract;
            // filter by contract
            } else {

                // list internal txs
                if ($internal) {
                    $txlist_endpoint = "txlistinternal";
                    $offset = 10; // internal txs are slower, so lets show less
                // list regular txs
                } else {
                    $txlist_endpoint = "txlist";
                    $offset = self::paginationOffset;
                }
                $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=' . $txlist_endpoint . '&address=' . $contract . 
                '&page=' . $page . '&offset=' . $offset . '&sort=desc&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key
            }

//            return $etherscan_url;

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == "200") {

                $txlist = json_decode($response["body"], true);

                if (is_array($txlist) and array_key_exists('result', $txlist)) {

                    $txs=[];
                    $localeconv = localeconv();

                    // filter transactions with the contract
                    foreach ($txlist['result'] as $res) {

                        $txs_column = array_column($txs, 'txid');
                        if (array_search($res["hash"], $txs_column)!==false) continue;

                        // parse etherscan input response
                        $hashFunction = '';
                        $to = '';
                        $value = 0;

                        list($hashFunction, $type, $transferFrom, $to, $value) = self::processInput($res["input"]);

                        // find the date format in settings or set default
                        $settings = WPSCSettingsPage::get();
                        if (!$date_format = $settings["date_format"]) {
                            $date_format = 'Y-m-d';
                        }
                        
                        $from = $res["from"];

                        if ($type=="transferFrom" or $type=="burnFrom") {
                            $from = $transferFrom;
                        }

                        // if this is an internal request then  find internal details
                        if ($internal) {
                            $txs[] = current(self::getTxId([
                                "network"=>$network, 
                                "contract"=>$contract, 
                                "txid"=>$res["hash"],
                                "ignore_contract"=>true // in this case we dont want to filter by contract
                            ]));
                        // otherwise return regular fields
                        } else {

                            // use default to address if not in input
                            if (!$to) {
                                $to = $res["to"];
                            }
                            
                            // ad it to the tx list
                            $txs[] = [
                                'blockNumber' => $res["blockNumber"],
                                'timeStamp' => ($res["timeStamp"])?date($date_format, $res["timeStamp"]):'',
                                'txid' => $res["hash"],
                                'txid_short' => WPSC_helpers::shortify($res["hash"]),
                                'from' => $from,
                                'from_short' => WPSC_helpers::shortify($from),
                                'transfer_from' => $transferFrom,
                                'transfer_from_short' => WPSC_helpers::shortify($transferFrom),
                                'hashFunction' => $hashFunction,
                                'type' => $type,
                                'to' => $to,
                                'to_short' => WPSC_helpers::shortify($to),
                                'value' => $value?WPSC_helpers::formatNumber($value):
                                    WPSC_helpers::formatNumber($res["value"]/1000000000000000000),
                                'isError' => WPSC_helpers::valArrElement($res, 'isError')?$res["isError"]:false,
                                'subdomain' => self::getNetworkSubdomain($network, ''),
                            ];

                        }

                    }

                    // save the wp transient api response
                    if (!empty($txs)) {
                        self::saveTransientResponse($network, $contract, $address, $page, $internal, $txs);
                    }

                    return $txs;
                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function getTxId($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $network = $params['network'];
        $contract = $params['contract'];
        $txid = $params['txid'];
        $ignore_contract = $params['ignore_contract'];

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $txid, null, null, null)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=proxy&action=eth_getTransactionByHash&txhash=' . 
                $txid . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $res = json_decode($response["body"], true);

                if (is_array($res) and array_key_exists('result', $res)) {
                    $res = $res["result"];
                }

                if (is_array($res) and array_key_exists('hash', $res)) {

                    // filtering contract interaction comparing unchecksummed addresses
                    if ($ignore_contract or strtolower($res["to"]) == strtolower($contract)) {

                        // parse etherscan input response
                        $hashFunction = '';
                        $to = '';
                        $type = false;
                        $value = 0;

                        list($hashFunction, $type, $transferFrom, $to, $value) = self::processInput($res["input"]);

                        // now try to get the block info to get the timestamp
                        $time_stamp = null;
                        if ($res["blockNumber"]) {

                            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=block&action=getblockreward&blockno=' . 
                                hexdec( substr($res["blockNumber"], 2) ) . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key


                            // hit the api
                            $response = wp_remote_get( $etherscan_url );

                            // successful?
                            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                                $body = json_decode($response["body"], true);
                                $time_stamp = $body["result"]["timeStamp"];

                            }

                        }

                        $txs[] = [
                            'blockNumber' => $res["blockNumber"],
                            'timeStamp' => ($time_stamp)?date('Y-m-d', $time_stamp):'',
                            'txid' => $res["hash"],
                            'txid_short' => WPSC_helpers::shortify($res["hash"]),
                            'from' => $res["from"],
                            'from_short' => WPSC_helpers::shortify($res["from"]),
                            'transfer_from' => $transferFrom,
                            'transfer_from_short' => WPSC_helpers::shortify($transferFrom),
                            'hashFunction' => $hashFunction,
                            'type' => $type,
                            'to' => $to,
                            'to_short' => WPSC_helpers::shortify($to),
                            'value' => $value?WPSC_helpers::formatNumber($value):
                                WPSC_helpers::formatNumber(hexdec($res["value"])/1000000000000000000),
                            'isError' => WPSC_helpers::valArrElement($res, 'isError')?$res["isError"]:false,
                            'subdomain' => self::getNetworkSubdomain($network, ''),
//                            'response' => $res,
                        ];
                    }

                    // save the wp transient api response
                    if (!empty($txs)) {
                        self::saveTransientResponse($network, $txid, null, null, null, $txs);
                    }

                    return $txs;
                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function getCode($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $network = $params['network'];
        $contract = $params['contract'];

        if (!$subdomain = self::getNetworkSubdomain($network)) {
            return [];
        }

        // if we have a transient stored, return it
        if ($txs = self::getTransientResponse($network, $contract, 'source_code', null, null)) {

            return $txs;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=contract&action=getsourcecode&address=' . 
                $contract . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $res = json_decode($response["body"], true);

                if (is_array($res) and array_key_exists('result', $res)) {
                    self::saveTransientResponse($network, $contract, 'source_code', null, null, $res["result"]);
                    return $res["result"];
                }

            }

        }

        return [];

    }

    // get total token supply
    public static function getTotalSupply($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        if (!$subdomain = self::getNetworkSubdomain($params['network'])) {
            return [];
        }

        // if we have a transient stored, return it
        if ($supply = self::getTransientResponse($params['network'], $params['contract'], "total_supply", null, null)) {

            return $supply;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=stats&action=tokensupply&contractaddress=' . $params['contract'] . '&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $supply = json_decode($response["body"], true);

                if (is_array($supply) and WPSC_helpers::valArrElement($supply, 'result')) {

                    $formatted_result = WPSC_helpers::formatNumber($supply["result"] / 1000000000000000000);

                    // save the wp transient api response
                    self::saveTransientResponse($params['network'], $params['contract'], "total_supply", null, null, $formatted_result);

                    return $formatted_result;
                    
                }

            }

        }

        return [];

    }

    // get balance of one holder
    public static function getBalance($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        if (!$subdomain = self::getNetworkSubdomain($params['network'])) {
            return [];
        }

        // if we have a transient stored, return it
        if ($balance = self::getTransientResponse($params['network'], $params['contract'], $params['address'] . "_balance", null, null)) {

            return $balance;

        // otherwise hit the Etherscan API
        } else {

            // do we have a key?
            $etherscan_api_key = get_option('etherscan_api_key_option');

            $etherscan_url = 'https://' . $subdomain . '.etherscan.io/api?module=account&action=tokenbalance&contractaddress=' . $params['contract'] . '&address=' . $params['address'] . '&tag=latest&apikey=' . trim($etherscan_api_key["api_key"]); // we try to use the user api key

            // hit the api
            $response = wp_remote_get( $etherscan_url );

            // successful?
            if ( is_array( $response ) and $response["response"]["code"] == 200) {

                $balance = json_decode($response["body"], true);

                if (is_array($balance) and array_key_exists('result', $balance)) {

                    $final_balance = WPSC_helpers::formatNumber($balance["result"] / 1000000000000000000);

                    // save the wp transient api response
                    self::saveTransientResponse($params['network'], $params['contract'], $params['address'] . "_balance", null, null, $final_balance);

                    return $final_balance;

                }

            }

        }

        return [];

    }

    // filter tx based on contract / account address
    public static function removeCache() {

        check_ajax_referer('wp_rest', '_wpnonce');
        global $wpdb;

        $current_user = wp_get_current_user();
        if (user_can( $current_user, 'administrator' )) {

            $wpdb->query("DELETE FROM `wp_options` WHERE option_name LIKE '%_transient_" . self::transientPrefix . "%'");
            return new WP_REST_Response(true);

        }

    }

    // format float
    public static function formatFloat($params) {

        check_ajax_referer('wp_rest', '_wpnonce');

        $float = $params['float'];

        if ($float) {
            return WPSC_helpers::formatNumber($float / 1000000000000000000);            
        } else {
            return 0;
        }
        
    }

    // NFT Token URI
    public static function nft($params) {

        $id = $params['id'];

        if (!$id) return;

        $wpsc_nft_id = get_post_meta($id, "wpsc_nft_id", true);

        if (!$wpsc_nft_id) return "Item is not deployed yet";

        $transient_name = "wpsc_nft_" . $id;

        if ($result = get_transient($transient_name)) {
            return $result;
        }

        $nft = get_post($id);

        $media_type = get_post_meta($id, "wpsc_media_type", true);

        if ( $wpsc_network = get_post_meta($id, 'wpsc_network', true) ) {
            list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
        }
        $collection_id = get_post_meta($id, 'wpsc_item_collection', true);
        if ($collection_id) {
            $contract = get_post_meta($collection_id, 'wpsc_contract_address', true);
            $link_etherscan = $etherscan."token/".$contract."?a=".$wpsc_nft_id;
        }

        $res = [
            "id"=>$id,
            "name" => $nft->post_title,
            "description" => strip_tags($nft->post_content),
            "attributes" => [
                ["trait_type" => "creator", "value" => "@".get_the_author_meta("display_name", $nft->post_author)],
                ["trait_type" => "last_modified_gmt", "value" => $nft->post_modified_gmt],
            ],
            "media-type" => $media_type,
            "external_url" => get_permalink($id),
            "author_id" => $nft->post_author,
            "author_external_url" => add_query_arg(["a" => $nft->post_author, "id"=>$collection_id], WPSC_assets::getNFTAuthorsPage()),
            "author_avatar" => get_avatar_url($nft->post_author),
            "author_account" => get_post_meta($id, 'wpsc_creator', true),
            "nft_id" => $wpsc_nft_id,
            "network" => $network_val,
            "network_url" => $link_etherscan,
        ];

        $media_urls = null;
        if ($media = get_post_meta($id, "wpsc_nft_media_json", true)) {

            $tmp = json_decode($media);
            if (is_array($tmp)) {
                foreach ($tmp as $url) {
                    if ($url->id) {
                        if ($ipfs = get_post_meta($url->id, 'wpsc_nft_ipfs', true)) {
                            $media_urls[]=$ipfs;
                        } elseif ($att_url = wp_get_attachment_url($url->id)) {
                            $media_urls[]=$att_url;
                        }
                    }
                }
            }
        }

        $cats =  wp_get_post_terms($id, "nft-taxonomy");
        if (is_array($cats)) {
            foreach ($cats as $i => $cat) {
                $ind = $i + 1;
                $res["attributes"][] = [
                    "trait_type" => "Category $ind", 
                    "value" => $cat->name,
                    "link" => add_query_arg("id", $collection_id, get_term_link($cat))
                ];
            }
        }

        $attrs =  wp_get_post_terms($id, "nft-tag");
        if (is_array($attrs)) {
            foreach ($attrs as $attr) {
                $res["attributes"][] = [
                    "value" => $attr->name,
                    "link" => add_query_arg("id", $collection_id, get_term_link($attr))
                ];
            }
        }

        switch($media_type) {
            case "image":
                if (is_array($media_urls) and array_key_exists(0, $media_urls)) {
                    $res["image"] = $media_urls[0];
                }
                break;
            default:
                if ($thumb = get_the_post_thumbnail_url($id)) {
                    $res["image"] = $thumb;
                }
                if (is_array($media_urls)) {
                    foreach($media_urls as $i=>$url) {
                        if ($i) {
                            $ind = $i + 1;
                            $res["animation_url".$ind] = $url;
                        } else {
                            $res["animation_url"] = $url;
                        }
                    }
                } elseif ($media_urls) {
                    $res["animation_url"] = $media_urls;
                }
                break;
        }
        
        set_transient($transient_name, $res);

        return $res;
        
    }

    static private function validateAddress($add, $len = 40) {
        return preg_match('/^(0x)?[0-9a-fA-F]{'.$len.'}$/i', $add);
    }

    // endpoint callback to create NFT on the interface
    public static function nftInsert($param) {

        $user = wp_get_current_user();

        $nickname = get_the_author_meta("display_name", $user->ID );

        // sanitize integer
        $nft_id = (int) $param['nft_id'];

        $collection_id = (int) $param['collection_id'];

        if ($error = WPSC_Shortcodes::validateNFTFE($collection_id, $user, $nft_id)) {
            return new WP_REST_Response($error , 200 );
        }

        // sanitize texts
        $title = sanitize_text_field($param['title']);
        if (!$title) return new WP_REST_Response("Title is not valid", 200);

        $tainted_custom_atts = $param['custom_atts'];

        $owner = sanitize_text_field($param['owner']);
        if (!self::validateAddress($owner))
            return new WP_REST_Response("The recipient is not a valid address", 200);

        // sanitize media json string
        $media = $param['media'];
        if (is_null(json_decode($media))) {
            $media = "";
        }
        $media_type = sanitize_text_field($param['media_type']);
        if (!in_array($media_type, ["image", "video", "audio", "document"])) {
            $media_type="";
        }
        
        // sanitize textarea keeping the line breaks
        $description = $param['description'];
        if ($description) {
            $description = implode( "\n", array_map( 'sanitize_textarea_field', explode( "\n", $description ) ) );
        }
        
        // sanitize numeric arrays
        $tags = self::integrify($param['tags'], $tainted_custom_atts);
        $categories = self::integrify($param['categories']);

        $arr = [
            'ID' => $nft_id,
            'post_author' => $user->ID,
            'post_content' => $description,
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'nft'
        ];

        if ($the_id = wp_insert_post($arr)) {
            update_post_meta($the_id, "wpsc_nft_owner", $owner);
            update_post_meta($the_id, "wpsc_item_collection", $collection_id);
            update_post_meta($the_id, "wpsc_nft_media_json", $media);
            update_post_meta($the_id, "original_author", $nickname);
            update_post_meta($the_id, "original_author_id", $user->ID);
            update_post_meta($the_id, "wpsc_media_type", $media_type);            
            wp_set_object_terms( $the_id, $categories, 'nft-taxonomy', false );
            wp_set_object_terms( $the_id, $tags, 'nft-tag', false );

            // clear cache
            delete_transient("wpsc_nft_" . $nft_id);

            return new WP_REST_Response($the_id, 200 );
        } else {
            return new WP_REST_Response("An error ocurred inserting the NFT", 200 );
        }

    }

    public static function nftLog($param) {

        $post_id = (int) $param['id'];
        $txid = sanitize_text_field($param['txid']);
        $to = sanitize_text_field($param['to']);
        if (is_numeric($param['value'])) {
            $value = $param['value'];
        } else {
            $value = 0;
        }
        $date = date("Y-m-d H:i:s");
        if (!$post_id or !$value) return new WP_REST_Response("Invalid values" , 200 );
        if (!self::validateAddress($to) or !self::validateAddress($txid, 64)) return new WP_REST_Response("Invalid data" , 200 );
        if (get_post_type($post_id)!="nft") return new WP_REST_Response("Invalid post" , 200 );

        $log_history = json_decode(get_post_meta($post_id, 'wpsc_log_history', true), true);
        if (empty($log_history)) {
            $log_history = [];
        }
        $log_history[] = ["txid"=>$txid, "to"=>$to, "value"=>$value, "date"=>$date];
        update_post_meta($post_id, "wpsc_log_history", json_encode($log_history));

    }
    
    public static function isMinted($param) {

        $user = wp_get_current_user();

        // sanitize integer
        $nft_id = (int) $param['id'];

        $nft_id_check = (int) get_post_meta($nft_id, 'wpsc_nft_id', true);

        if ($nft_id_check>0) {
            return new WP_REST_Response("true" , 200 );
        } else {
            return new WP_REST_Response("false" , 200 );
        }

    }

    public static function nftGetByIDs($param) {

        // sanitize integer
        $collection_id = (int) $param['collid'];

        $contract = get_post_meta($collection_id, 'wpsc_contract_address', true);

        if (!$contract) return "Collection is not deployed";
        if (!$collection_id) return "Invalid collection ID";
		if (get_post_type($collection_id)!="nft-collection") return "Invalid collection ID";

        $arr_unsanitized = explode("-", $param['ids']);

        $nfts = '';

        if (is_array($arr_unsanitized)) {
            foreach($arr_unsanitized as $elem_unsanitized) {
                $elem = (int) $elem_unsanitized;
                if ($elem) {
                    if ($nfts) $nfts .= ",";
                    $nfts .= $elem;
                }
            }
        }

        if (!empty($nfts) and !empty($contract)) {

            $transient_id = $contract."_".$nfts;
            if ($response = self::getGenericTransientResponse($transient_id)) return new WP_REST_Response($response, 200 );
            
            if ($response = WPSC_Queries::getNFTsByID($nfts, $contract)) {
                $token_uris = [];
                if (is_array($response)) {
                    foreach($response as $p) {
                        $token_uris[] = [
                            "nft" => self::nft(["id"=>$p["ID"]]),
                            "json" => get_post_meta($p["ID"], 'wpsc_nft_media_json', true),
                            "media_type" => get_post_meta($p["ID"], 'wpsc_media_type', true)
                        ];
                    }
                }
                self::saveGenericTransientResponse($transient_id, $token_uris);
                return new WP_REST_Response($token_uris, 200);
            }

        }

        return new WP_REST_Response([], 200 );

    }

    public static function nftExistsIPFS($param) {
        
        // get attachment ID
        $attachment_id = (int) $param['id'];
        if (!$attachment_id or get_post_type($attachment_id)!="attachment") return new WP_REST_Response(false, 200 );

        // verify if the attachment was deployed already
        $wpsc_nft_ipfs = get_post_meta($attachment_id, 'wpsc_nft_ipfs', true);

        if ($wpsc_nft_ipfs) return new WP_REST_Response(true , 200 );

        return new WP_REST_Response(false , 200 );

    }

    public static function nftIPFSStore($param) {
        
        // get attachment ID
        $attachment_id = (int) $param['attachment_id'];
        if (!$attachment_id or get_post_type($attachment_id)!="attachment") return new WP_REST_Response("Invalid attachment", 200 );

        // verify if the attachment was deployed already
        $wpsc_nft_ipfs = get_post_meta($attachment_id, 'wpsc_nft_ipfs', true);
        if ($wpsc_nft_ipfs) return new WP_REST_Response($wpsc_nft_ipfs , 200 );

        // get nft storage api key
        $options = get_option('etherscan_api_key_option');
        $nft_storage_key = (WPSC_helpers::valArrElement($options, "nft_storage_key") and !empty($options["nft_storage_key"]))?$options["nft_storage_key"]:false;
        if (!$nft_storage_key) return new WP_REST_Response("Invalid NFT storage key. Are you the system admin? Please setup the nft.storage keys in your WP Smart Contracts settings.", 200 );

        // get file content
        $file = file_get_contents( get_attached_file( $attachment_id ) );
        if (!$file) return new WP_REST_Response("Invalid attachment", 200 );

        // call the NFT Storage endpoint
        $api = new \RestClient( [ 'base_url' => 'https://api.nft.storage' ] );
        $result = $api->post( '/upload', $file, ['Authorization' => 'Bearer ' . $nft_storage_key] );

        if ( 200 === $result->info->http_code ) {
            $wpsc_nft_ipfs = "https://ipfs.io/ipfs/" . $result->decode_response()->value->cid;

            // store this on the attachment post
            update_post_meta($attachment_id, 'wpsc_nft_ipfs', $wpsc_nft_ipfs);

            // clear transient cache for NFT endpoints
            WPSC_Queries::clearNFTTokenURI();

            return new WP_REST_Response("SUCCESS,".$wpsc_nft_ipfs, 200 );
        } else {
            return new WP_REST_Response($result->error, 200 );
        }

    }
    
    // endpoint callback to create NFT on the interface
    public static function nftSaveDeploy($param) {

        $user = wp_get_current_user();

        // sanitize integer
        $nft_id = (int) $param['nft_id'];

        $collection_id = (int) $param['wpsc-item-collection'];
        if ($error = WPSC_Shortcodes::validateNFTFE($collection_id, $user, $nft_id)) {
            return new WP_REST_Response($error , 200 );
        }

        $nft_id_check = (int) get_post_meta($nft_id, 'wpsc_nft_id', true);

        if ($nft_id_check>0) {
            return new WP_REST_Response("Item was already minted" , 200 );
        }

        $wpsc_collection_contract = WPSC_Metabox::cleanUpText($param["wpsc-collection-contract"]);
        update_post_meta($nft_id, 'wpsc_collection_contract', $wpsc_collection_contract);
    
        $wpsc_item_collection = WPSC_Metabox::cleanUpText($param["wpsc-item-collection"]);
        update_post_meta($nft_id, 'wpsc_item_collection', $wpsc_item_collection);
    
        $wpsc_network = WPSC_Metabox::cleanUpText($param["wpsc-network"]);
        update_post_meta($nft_id, 'wpsc_network', $wpsc_network);
    
        $wpsc_txid = WPSC_Metabox::cleanUpText($param["wpsc-txid"]);
        update_post_meta($nft_id, 'wpsc_txid', $wpsc_txid);
    
        $wpsc_creator = WPSC_Metabox::cleanUpText($param["wpsc-creator"]);
        update_post_meta($nft_id, 'wpsc_creator', $wpsc_creator);
    
        $wpsc_creator_blockie = WPSC_Metabox::cleanUpText($param["wpsc-creator-blockie"]);
        update_post_meta($nft_id, 'wpsc_creator_blockie', $wpsc_creator_blockie);
    
        $wpsc_nft_id = WPSC_Metabox::cleanUpText($param["wpsc-nft-id"]);
        update_post_meta($nft_id, 'wpsc_nft_id', $wpsc_nft_id);
    
        $wpsc_nft_id_blockie = WPSC_Metabox::cleanUpText($param["wpsc-nft-id-blockie"]);
        update_post_meta($nft_id, 'wpsc_nft_id_blockie', $wpsc_nft_id_blockie);
    
        $wpsc_nft_url = WPSC_Metabox::cleanUpText($param["wpsc-nft-url"]);
        update_post_meta($nft_id, 'wpsc_nft_url', $wpsc_nft_url);

        return new WP_REST_Response(get_permalink($nft_id), 200 );

    }

    static private function getNetworkSubdomain($network, $prefix="api") {

        // translate network

        if ( $arr = WPSC_helpers::getNetworks() ) {

            if ($network==1) {
                return $prefix;
            } else {
                if ($prefix) return $prefix . '-' . $arr[$network]["name"];
                else  return $arr[$network]["name"];
            }

        }

    }

    // get txs stored in wp transient
    private static function getTransientResponse($network, $contract, $address, $page, $internal) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $network . "_" . $contract . "_" . $address . "_" . $page . "_" . $internal;

        if ($t = get_transient($transient_name)) {
            return $t;
        } else {
            return false;
        }

    }

    // store txs to wp transient
    private static function saveTransientResponse($network, $contract, $address, $page, $internal, $txs) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $network . "_" . $contract . "_" . $address . "_" . $page . "_" . $internal;

        set_transient($transient_name, $txs, $expiration_time);

    }

    private static function getGenericTransientResponse($id) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $id;

        if ($t = get_transient($transient_name)) {
            return $t;
        } else {
            return false;
        }

    }

    // store txs to wp transient
    private static function saveGenericTransientResponse($id, $content) {

        // cache is activated?
        if (!$expiration_time = WPSCSettingsPage::get('expiration_time')) {
            return false;
        }

        $transient_name = self::transientPrefix . $id;

        set_transient($transient_name, $content, $expiration_time);

    }

    static private function integrify($arr, $tainted_custom_atts=false) {
        
        if (!$tainted_custom_atts and !is_array($arr)) return [];

        $new = [];

        if ($tainted_custom_atts) {

            $arr_atts = explode(",", $tainted_custom_atts);
            if (is_array($arr_atts)) {

                foreach($arr_atts as $i => $att) {

                    $sanitized_att = sanitize_text_field($att);
                    $sanitized_att = trim($att);

                    if ($att and strlen($sanitized_att)<30) {
                        if ($res = term_exists($sanitized_att, "nft-tag")) {
                            if ($term_id = $res["term_id"] and array_search($term_id, $new)===false) {
                                $new[] = (int) $term_id;
                            }
                        } else {
                            error_log("wp_insert_term($sanitized_att");
                            $res = wp_insert_term($sanitized_att, "nft-tag");
                            if (!is_wp_error($res) and $term_id = $res["term_id"] and array_search($term_id, $new)===false) {
                                $new[] = (int) $term_id;
                            }
                        }
                    }
                    if ($i==9) {
                        break;
                    }
                }
                 
            }
        }

        if (is_array($arr)) {
            foreach($arr as $i) {
                $j = (int) $i;
                if ($j and array_search($j, $new)===false) {
                    $new[]=$j;
                }
            }    
        }

        error_log("new " . print_r($new, true));
        return $new;

    }

}
