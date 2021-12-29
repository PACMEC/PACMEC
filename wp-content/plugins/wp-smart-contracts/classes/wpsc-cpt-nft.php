<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Flush permalinks after first NFT save
 */ 
add_action( 'save_post', function ( $post_id, $post, $update ) {

    $post_type = get_post_type($post_id);

    // If this isn't a 'nft' post, don't update it.
    if ( "nft" != $post_type ) return;

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
require_once("wpsc-metabox-nft.php");

/**
 * Create NFT Post Type
 */

new WPSC_NFTCPT();

class WPSC_NFTCPT {

    // Define the NFT CPT
    function __construct() {

        // create NFT
        add_action( 'init', [$this, 'initialize'] );
    
        // add extra columns to NFT view
        add_filter( 'manage_nft_posts_columns', [$this, 'setCustomEditNFTColumns'] );
        add_action( 'manage_nft_posts_custom_column' , [$this, 'customNFTColumn'], 10, 2 );

        // add column styles
        add_action('admin_head', [$this, 'myThemeAdminHead']);

        // Create a template view for the new CPT
        add_filter('single_template', [$this, 'setTemplateNFT'] );
        add_filter( 'taxonomy_template', [$this, 'setTemplateNFTTaxonomy'] );

        add_action('pre_get_posts', [$this, 'authorArchive'] );

    }

    // Create NFTs CPT
    public function initialize() {

        $labels = array(
            'name'               => _x( 'NFT', 'post type general name', 'wp-smart-contracts' ),
            'singular_name'      => _x( 'NFT', 'post type singular name', 'wp-smart-contracts' ),
            'menu_name'          => _x( 'NFT', 'admin menu', 'wp-smart-contracts' ),
            'name_admin_bar'     => _x( 'NFT', 'add new on admin bar', 'wp-smart-contracts' ),
            'add_new'            => _x( 'Add New NFT', 'NFT', 'wp-smart-contracts' ),
            'add_new_item'       => __( 'Add New NFT', 'wp-smart-contracts' ),
            'new_item'           => __( 'New NFT', 'wp-smart-contracts' ),
            'edit_item'          => __( 'Edit NFT', 'wp-smart-contracts' ),
            'view_item'          => __( 'View NFT', 'wp-smart-contracts' ),
            'all_items'          => __( 'All NFT', 'wp-smart-contracts' ),
            'search_items'       => __( 'Search NFT', 'wp-smart-contracts' ),
            'parent_item_colon'  => __( 'Parent NFT:', 'wp-smart-contracts' ),
            'not_found'          => __( 'No NFT found.', 'wp-smart-contracts' ),
            'not_found_in_trash' => __( 'No NFT found in Trash.', 'wp-smart-contracts' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'wp-smart-contracts' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,   // this removes UI from wp-admin
            'show_in_menu'       => true,
            'menu_icon'          => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-nft.png',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'nft' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail' )
        );

        register_post_type( 'nft', $args );

        register_taxonomy(
            'nft-taxonomy',
            array('nft'), 
            array(
                'hierarchical' => true,
                'labels' => [
                    'name' => _x( 'Taxonomies', 'taxonomy general name' ),
                    'singular_name' => _x( 'Taxonomy', 'taxonomy singular name' ),
                    'search_items' =>  __( 'Search Taxonomies' ),
                    'all_items' => __( 'All Taxonomies' ),
                    'parent_item' => __( 'Parent Taxonomy' ),
                    'parent_item_colon' => __( 'Parent Taxonomy:' ),
                    'edit_item' => __( 'Edit Taxonomy' ),
                    'update_item' => __( 'Update Taxonomy' ),
                    'add_new_item' => __( 'Add New Taxonomy' ),
                    'new_item_name' => __( 'New Taxonomy Name' ),
                    'menu_name' => __( 'Taxonomies' )
                ],
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite' => array( 
                    'slug' => 'nft-taxonomy' 
                )
            )
        );
        register_taxonomy(
            'nft-tag',
            array('nft'), 
            array(
                'hierarchical' => false,
                'labels' => [
                    'name' => _x( 'Attributes', 'Attribute general name' ),
                    'singular_name' => _x( 'Attribute', 'Attributes singular name' ),
                    'search_items' =>  __( 'Search Attributes' ),
                    'all_items' => __( 'All Attributes' ),
                    'edit_item' => __( 'Edit Attribute' ),
                    'update_item' => __( 'Update Attributes' ),
                    'add_new_item' => __( 'Add New Attributes' ),
                    'new_item_name' => __( 'New Attributes Name' ),
                    'menu_name' => __( 'Attributes' ),
                    'separate_items_with_commas' => __('Specify the attributes of your Item. Separate them with commas.'),
                    'choose_from_most_used' => __('Choose from the most used attributes')
                ],
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite' => array( 
                    'slug' => 'nft-tag' 
                )
            )
        );

    }

    public function setTemplateNFT ($template) {
        global $post;
        if ( $post->post_type == 'nft' ) {
            return self::generateCustomTemplate('nft.php');
        }
        return $template;
    }

    public function setTemplateNFTTaxonomy( $template ) {
        if (is_tax( 'nft-taxonomy')) {
            return self::generateCustomTemplate('nft-taxonomy.php');
        }
        if (is_tax( 'nft-tag')) {
            return self::generateCustomTemplate('nft-tag.php');
        }
        return $template;
    }

    public function authorArchive($query) {
        if ($query->is_author) {
            $query->set( 'post_type', ['nft'] );
        }
        remove_action( 'pre_get_posts', 'custom_post_author_archive' );
    }

    // Define column headers in the CPT list
    public function setCustomEditNFTColumns($columns) {
    
        unset($columns['date']);   // remove date from the columns list

        $columns['the_author'] = __( 'Author', 'wp-smart-contracts' );
        $columns['collection'] = __( 'Collection', 'wp-smart-contracts' );
        $columns['nft_id'] = __( 'NFT ID', 'wp-smart-contracts' );
        $columns['the_type'] = __( 'Type', 'wp-smart-contracts' );
        $columns['network'] = __( 'Network', 'wp-smart-contracts' );

        $columns['date'] = __( 'Date', 'wp-smart-contracts' ); // now add date to the end

        return $columns;

    }

    // Define the values of each column in the CPT list
    public function customNFTColumn( $column, $post_id ) {

        $m = new Mustache_Engine;

        if ( $wpsc_network = get_post_meta($post_id, 'wpsc_network', true) ) {
            list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
        }

        switch ( $column ) {
            case 'the_type' :
                $label = get_post_meta ($post_id, 'wpsc_media_type', true);
                if ($label) {
                    switch ($label) {
                        case 'video':
                            $color = "blue";
                            $icon = "film";
                            break;
                        case 'audio':
                            $color = "green";
                            $icon = "headphones";
                            break;
                        case 'document':
                            $color = "olive";
                            $icon = "file outline";
                            break;
                        case 'image':
                            $color = "teal";
                            $icon = "image outline";
                            break;
                        default:
                            $color = "";
                            $icon = "circle outline";
                            break;
                    }
                    if (!$label or $label<>"none") {
                        echo "<div class=\"ui image $color label\">";
                        echo "<i class=\"$icon icon\"></i>";
                        echo ucwords($label);
                        echo '</div>';    
                    }
                }
                break;

            case 'the_author' :
                $author_id = get_post_field ('post_author', $post_id);
                ?>
                <div class="ui image label">
                    <?=get_avatar($author_id)?>
                    <a href="<?=get_the_author_meta( 'url' , $author_id )?>">
                        @<?=get_the_author_meta( 'user_nicename' , $author_id )?>
                    </a>
                </div>
                <?php
                    if ( $wpsc_network = get_post_meta($post_id, 'wpsc_network', true) ) {
                        list($color, $icon, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);
                    }
                    $add = get_post_meta( $post_id, 'wpsc_creator' , true );
                    if ($add) {
                        $link = $etherscan . "address/" . $add;
                        echo "<br><i class=\"copy icon\" onclick=\"copyToClipboard('".$add."', 'wpsc-qr-".$post_id."')\"></i>";
                        echo "<span id=\"wpsc-qr-".$post_id."\"></span>";
                        echo "<a href=\"$link\" target=\"_blank\">" . WPSC_helpers::shortify($add) . "</a>";    
                    }
                break;

            case 'collection' :
                $collection = get_post_meta($post_id, 'wpsc_item_collection', true);
                if ($collection) {
                    echo "<a href=\"".get_edit_post_link($collection)."\">";
                    echo get_the_title($collection);
                    echo "</a>";
                }
                break;

            case 'nft_id' :
                if ($nft_id = get_post_meta($post_id, 'wpsc_nft_id', true)) {
                    $atts['contract'] = get_post_meta($post_id, 'wpsc_collection_contract', true);
                    $atts['id'] = $nft_id;
                    if ($blockie = get_post_meta($post_id, 'wpsc_nft_id_blockie', true)) {
                        $atts['blockie'] = $blockie;
                    }
                    $atts["etherscan"] = $etherscan;
                    echo $m->render(WPSC_Mustache::getTemplate('contract-identicons-nft'), $atts);
                } else {
                    echo '';
                }
                break;

            case "network":
                if (isset($network_val) and $network_val) {
                    echo "<div class=\"ui small $color label fluid\">$network_val</div>";
                }
                break;

        }

    }

    // define the size of specific columns in the CPT list
    public function myThemeAdminHead() {
        global $post_type;
        if ( 'nft' == $post_type ) {
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

