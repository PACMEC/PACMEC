<?php
/*
Plugin Name: WP Smart Contracts
Plugin URI: https://www.wpsmartcontracts.com/
Description: Easily create powerful Smart Contracts in Ethereum, Binance Smart Chain, Polygon and more.
Version: 1.2.12
Author: WPSmartContracts.com
Text Domain: wp-smart-contracts
Domain Path: /languages
License: GPLv2 or later
*/

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Load generic helpers
 */
require_once("classes/wpsc-helpers.php");

/**
 * Template system, in JS and PHP
 */
require_once("classes/wpsc-mustache.php");

/**
 * Create Custom Post Types
 */
require_once("classes/wpsc-cpt.php");

/**
 * Load css and js
 */
require_once("classes/wpsc-assets.php");

/**
 * Load shortcodes
 */
require_once("classes/wpsc-shortcodes.php");

/**
 * Load Queries
 */
require_once("classes/wpsc-queries.php");

/**
 * Load wp-admin settings
 */
require_once("classes/wpsc-settings.php");

/**
 * Load Block Explorer Options
 */
require_once("classes/wpsc-endpoints.php");

/**
 * Load Clean Template
 */
require_once("classes/wpsc-page-templater.php");

/**
 * Create QR Scanner page on plugin activate
 */
add_action('init', function() {
    WPSC_helpers::createPluginPages();
});

/**
 * IPFS
 */
require_once(__DIR__ . "/classes/wpsc-rest-client.php");
require_once(__DIR__ . "/classes/wpsc-media-ipfs.php");

/**
 * Internal use only, load deployer
 */
if (file_exists(__DIR__ . "/classes/wpsc-deployer.php")) {
    require_once(__DIR__ . "/classes/wpsc-deployer.php");
}

