<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Flush permalinks after first NFT save
 */ 
add_action( 'save_post', function ( $post_id, $post, $update ) {

    $post_type = get_post_type($post_id);

    // If this isn't a 'coin' post, don't update it.
    if ( "nft-collection" != $post_type ) return;

    $option_name = "NFT_permalinks_flushed";

    if (!get_option($option_name)) {

        // clean up permalink settings
        flush_rewrite_rules();

        // flush only once
        update_option($option_name, true);

    }

}, 10, 3 );

/**
 * Load help metaboxes for NFT cpt
 */
require_once("wpsc-metabox-nft-collection.php");

/**
 * Create NFT Post Type
 */

new WPSC_NFTCollectionCPT();

class WPSC_NFTCollectionCPT {

    // Define the NFT CPT
    function __construct() {

        // create NFT
        add_action( 'init', [$this, 'initialize'] );
    
        // add extra columns to NFT view
        add_filter( 'manage_nft-collection_posts_columns', [$this, 'setCustomEditNFTColumns'] );
        add_action( 'manage_nft-collection_posts_custom_column' , [$this, 'customNFTColumn'], 10, 2 );

        // add column styles
        add_action('admin_head', [$this, 'myThemeAdminHead']);

        // Create a template view for the new CPT
        add_filter('single_template', [$this, 'setTemplateNFT'] );

    }

    // Create NFTs CPT
    public function initialize() {

        $labels = array(
            'name'               => _x( 'NFT Collections', 'post type general name', 'wp-smart-contracts' ),
            'singular_name'      => _x( 'NFT Collection', 'post type singular name', 'wp-smart-contracts' ),
            'menu_name'          => _x( 'NFT Collections', 'admin menu', 'wp-smart-contracts' ),
            'name_admin_bar'     => _x( 'NFT Collection', 'add new on admin bar', 'wp-smart-contracts' ),
            'add_new'            => _x( 'Add New Collection', 'NFT', 'wp-smart-contracts' ),
            'add_new_item'       => __( 'Add New Collection', 'wp-smart-contracts' ),
            'new_item'           => __( 'New Collection', 'wp-smart-contracts' ),
            'edit_item'          => __( 'Edit Collection', 'wp-smart-contracts' ),
            'view_item'          => __( 'View Collection', 'wp-smart-contracts' ),
            'all_items'          => __( 'All Collections', 'wp-smart-contracts' ),
            'search_items'       => __( 'Search Collection', 'wp-smart-contracts' ),
            'parent_item_colon'  => __( 'Parent Collections:', 'wp-smart-contracts' ),
            'not_found'          => __( 'No Collections found.', 'wp-smart-contracts' ),
            'not_found_in_trash' => __( 'No Collections found in Trash.', 'wp-smart-contracts' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'wp-smart-contracts' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-nft-collection.png',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'nft-collection' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail' )
        );

        register_post_type( 'nft-collection', $args );

    }

    public function setTemplateNFT ($template) {
        global $post;
        if ( $post->post_type == 'nft-collection' ) {
            return self::generateCustomTemplate('nft-collection.php');
        }
        return $template;
    }

    // Define column headers in the CPT list
    public function setCustomEditNFTColumns($columns) {
    
        unset($columns['date']);   // remove date from the columns list

        $columns['symbol'] = __( 'Symbol', 'wp-smart-contracts' );
        $columns['name'] = __( 'Name', 'wp-smart-contracts' );
        $columns['smart-contract'] = __( 'Smart Contract', 'wp-smart-contracts' );
        $columns['network'] = __( 'Network', 'wp-smart-contracts' );
        $columns['flavor'] = __( 'Flavor', 'wp-smart-contracts' );

        $columns['date'] = __( 'Date', 'wp-smart-contracts' ); // now add date to the end

        return $columns;

    }

    // Define the values of each column in the CPT list
    public function customNFTColumn( $column, $post_id ) {

        $m = new Mustache_Engine;

        switch ( $column ) {
            case 'symbol' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_symbol', true));
                break;

            case 'name' :
                echo strtoupper(get_post_meta($post_id, 'wpsc_name', true)); 
                break;

            case 'smart-contract' :

                if ( $wpsc_network = get_post_meta(get_the_ID(), 'wpsc_network', true) ) {
                    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
                }

                if ($contract = get_post_meta($post_id, 'wpsc_contract_address', true)) {

                    $atts['contract'] = $contract;
                    $atts['short-contract'] = substr($contract, 0, 8) . '...' . substr($contract, -6);
                    $atts['id'] = $post_id;

                    if ($blockie = get_post_meta($post_id, 'wpsc_blockie', true)) {
                        $atts['blockie'] = $blockie;
                    }
                    if ($qr = get_post_meta($post_id, 'wpsc_qr_code', true)) {
                        $atts['qr-code'] = $qr;
                    }
                    $atts["etherscan"] = $etherscan;
                    echo $m->render(WPSC_Mustache::getTemplate('contract-identicons-be'), $atts);
                } else {
                    echo '';
                }
                break;

            case 'network':
                if ( $wpsc_network = get_post_meta(get_the_ID(), 'wpsc_network', true) ) {
                    list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
                    echo $m->render(WPSC_Mustache::getTemplate('network'), 
                        [
                            "color" => $color,
                            "network_val" => $network_val,
                        ]
                    );
                } else {
                    echo "";
                }

                break;

            case 'flavor' :

                if ( $wpsc_flavor = get_post_meta(get_the_ID(), 'wpsc_flavor', true) ) {

                    $color = false;
                    if ($wpsc_flavor=="mochi") $color = "purple";
                    if ($wpsc_flavor=="suika") $color = "red";
                    if ($wpsc_flavor=="matcha") $color = "green";

                    echo $m->render('<a class="ui {{color}} label"><span class="wpsc-capitalize">{{flavor}}</span></a>', 
                    [
                        'color' => $color,
                        'flavor' => $wpsc_flavor,
                    ]);

                } else {
                    echo "";
                }
                break;

        }

    }

    // define the size of specific columns in the CPT list
    public function myThemeAdminHead() {
        global $post_type;
        if ( 'nft-collection' == $post_type ) {
            ?>
            <style type="text/css">
                .column-smart-contract { width: 30%; } 
            </style>
            <?php
        }
    }

    public static function generateCustomTemplate($php_file) {
        $the_file = dirname(__FILE__);
        $wpsc_plugin_path = plugin_dir_path($the_file, basename(dirname($the_file)) . '/' . basename($the_file)) . $php_file;
        if ( file_exists( $wpsc_plugin_path ) ) {
            return $wpsc_plugin_path;
        }
    }

}

