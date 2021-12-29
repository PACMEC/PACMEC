<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Create Metaboxes for CPT NFT
 */

new WPSC_MetaboxNFTCollection();

class WPSC_MetaboxNFTCollection {

  function __construct() {

    // load all custom fields
    add_action('admin_init', [$this, 'loadMetaboxes'], 2);

    // save repeatable fields
    add_action('save_post', [$this, 'saveRepeatableFields'], 10, 3);

  }

  public function loadMetaboxes() {

    $post_id = WPSC_helpers::valArrElement($_GET, "post")?sanitize_text_field($_GET["post"]):false;

    add_meta_box(
      'wpsc_nft_metabox', 
      'WPSmartContracts: NFT Specification', 
      [$this, 'wpscSmartContractSpecification'], 
      'nft-collection', 
      'normal', 
      'default'
    );
    
    add_meta_box(
      'wpsc_smart_contract', 
      'WPSmartContracts: Smart Contract', 
      [$this, 'wpscSmartContract'], 
      'nft-collection', 
      'normal', 
      'default'
    );
    
    add_meta_box(
      'wpsc_sidebar', 
      'WPSmartContracts: Tutorials & Tools', 
      [$this, 'wpscSidebar'], 
      'nft-collection', 
      'side', 
      'default'
    );

    add_meta_box(
      'wpsc_code_crowd', 
      'WPSmartContracts: Source Code', 
      [$this, 'wpscSourceCode'], 
      'nft-collection', 
      'normal', 
      'default'
    );

    add_meta_box(
      'wpsc_reminder_crowd', 
      'WPSmartContracts: Friendly Reminder', 
      [__CLASS__, 'wpscReminder'], 
      'nft-collection', 
      'normal', 
      'default'
    );

  }

  public function saveRepeatableFields($post_id, $post, $update) {

    if ($post->post_type == "nft-collection") {

      if ( ! isset( $_POST['wpsc_repeatable_meta_box_nonce'] ) ||
      ! wp_verify_nonce( $_POST['wpsc_repeatable_meta_box_nonce'], 'wpsc_repeatable_meta_box_nonce' ) )
          return;

      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
          return;

      if (!current_user_can('edit_post', $post_id))
          return;

      // if the contract was not deployed yet, save the nft definitions
      self::saveNFTMetaData($post_id, $_POST);

    }

  }

  public static function saveNFTMetaData($post_id, $arr) {

    // get clean and update all inputs
    if (!$arr["wpsc-readonly"]) {

      $wpsc_flavor = WPSC_Metabox::cleanUpText($arr["wpsc-flavor"]);
      update_post_meta($post_id, 'wpsc_flavor', $wpsc_flavor);

      $wpsc_symbol = WPSC_Metabox::cleanUpText($arr["wpsc-symbol"]);
      update_post_meta($post_id, 'wpsc_symbol', $wpsc_symbol);
      
      $wpsc_name = WPSC_Metabox::cleanUpText($arr["wpsc-name"]);
      update_post_meta($post_id, 'wpsc_name', $wpsc_name);
    
    }

    $wpsc_anyone_can_mint = WPSC_Metabox::cleanUpText($arr["wpsc-anyone-can-mint"]);
    update_post_meta($post_id, 'wpsc_anyone_can_mint', $wpsc_anyone_can_mint);
    
    $wpsc_anyone_can_author = WPSC_Metabox::cleanUpText($arr["wpsc-anyone-can-author"]);
    update_post_meta($post_id, 'wpsc_anyone_can_author', $wpsc_anyone_can_author);

    $wpsc_commission = WPSC_Metabox::cleanUpText($arr["wpsc-commission"]);
    update_post_meta($post_id, 'wpsc_commission', $wpsc_commission);
    
    $wpsc_royalties = WPSC_Metabox::cleanUpText($arr["wpsc-royalties"]);
    update_post_meta($post_id, 'wpsc_royalties', $wpsc_royalties);
    
    $wpsc_wallet = WPSC_Metabox::cleanUpText($arr["wpsc-wallet"]);
    update_post_meta($post_id, 'wpsc_wallet', $wpsc_wallet);

    $wpsc_token = WPSC_Metabox::cleanUpText($arr["wpsc-token"]);
    update_post_meta($post_id, 'wpsc_token', $wpsc_token);

    $wpsc_show_header = WPSC_Metabox::cleanUpText($arr["wpsc-show-header"]);
    update_post_meta($post_id, 'wpsc_show_header', $wpsc_show_header);
    
    $wpsc_show_breadcrumb = WPSC_Metabox::cleanUpText($arr["wpsc-show-breadcrumb"]);
    update_post_meta($post_id, 'wpsc_show_breadcrumb', $wpsc_show_breadcrumb);
    
    $wpsc_show_category = WPSC_Metabox::cleanUpText($arr["wpsc-show-category"]);
    update_post_meta($post_id, 'wpsc_show_category', $wpsc_show_category);
    
    $wpsc_show_id = WPSC_Metabox::cleanUpText($arr["wpsc-show-id"]);
    update_post_meta($post_id, 'wpsc_show_id', $wpsc_show_id);
    
    $wpsc_show_tags = WPSC_Metabox::cleanUpText($arr["wpsc-show-tags"]);
    update_post_meta($post_id, 'wpsc_show_tags', $wpsc_show_tags);
    
    $wpsc_show_owners = WPSC_Metabox::cleanUpText($arr["wpsc-show-owners"]);
    update_post_meta($post_id, 'wpsc_show_owners', $wpsc_show_owners);
    
    $wpsc_columns_n = WPSC_Metabox::cleanUpText($arr["wpsc-columns-n"]);
    update_post_meta($post_id, 'wpsc_columns_n', $wpsc_columns_n);
    
    $wpsc_font_main_color = WPSC_Metabox::cleanUpText($arr["wpsc-font-main-color"]);
    update_post_meta($post_id, 'wpsc_font_main_color', $wpsc_font_main_color);

    $wpsc_tag_bg_color = WPSC_Metabox::cleanUpText($arr["wpsc-tag-bg-color"]);
    update_post_meta($post_id, 'wpsc_tag_bg_color', $wpsc_tag_bg_color);
    
    $wpsc_tag_color = WPSC_Metabox::cleanUpText($arr["wpsc-tag-color"]);
    update_post_meta($post_id, 'wpsc_tag_color', $wpsc_tag_color);
    
    $wpsc_cat_bg_color = WPSC_Metabox::cleanUpText($arr["wpsc-cat-bg-color"]);
    update_post_meta($post_id, 'wpsc_cat_bg_color', $wpsc_cat_bg_color);
    
    $wpsc_cat_color = WPSC_Metabox::cleanUpText($arr["wpsc-cat-color"]);
    update_post_meta($post_id, 'wpsc_cat_color', $wpsc_cat_color);
    
    $wpsc_graph_bg_color = WPSC_Metabox::cleanUpText($arr["wpsc-graph-bg-color"]);
    update_post_meta($post_id, 'wpsc_graph_bg_color', $wpsc_graph_bg_color);
    
    $wpsc_graph_line_color = WPSC_Metabox::cleanUpText($arr["wpsc-graph-line-color"]);
    update_post_meta($post_id, 'wpsc_graph_line_color', $wpsc_graph_line_color);
    
    $wpsc_font_secondary_color = WPSC_Metabox::cleanUpText($arr["wpsc-font-secondary-color"]);
    update_post_meta($post_id, 'wpsc_font_secondary_color', $wpsc_font_secondary_color);
    
    $wpsc_background_color = WPSC_Metabox::cleanUpText($arr["wpsc-background-color"]);
    update_post_meta($post_id, 'wpsc_background_color', $wpsc_background_color);

    $wpsc_nft_roles = [];
    foreach ($_POST["roles"] as $key => $role) {
      $wpsc_nft_roles[]=$role;
    }
    update_post_meta($post_id, 'wpsc_nft_roles', $wpsc_nft_roles);

    if (!$arr["wpsc-readonly"]) {

      $wpsc_network = WPSC_Metabox::cleanUpText($arr["wpsc-network"]);
      $wpsc_txid = WPSC_Metabox::cleanUpText($arr["wpsc-txid"]);
      $wpsc_owner = WPSC_Metabox::cleanUpText($arr["wpsc-owner"]);
      $wpsc_contract_address = WPSC_Metabox::cleanUpText($arr["wpsc-contract-address"]);
      $wpsc_token_contract_address = WPSC_Metabox::cleanUpText($arr["wpsc-token-contract-address"]);
      $wpsc_factory = $arr["wpsc-factory"];
      $wpsc_blockie = WPSC_Metabox::cleanUpText($arr["wpsc-blockie"]);
      $wpsc_blockie_token = WPSC_Metabox::cleanUpText($arr["wpsc-blockie-token"]);
      $wpsc_blockie_owner = WPSC_Metabox::cleanUpText($arr["wpsc-blockie-owner"]);
      $wpsc_qr_code = WPSC_Metabox::cleanUpText($arr["wpsc-qr-code"]);
      $wpsc_token_qr_code = WPSC_Metabox::cleanUpText($arr["wpsc-token-qr-code"]);

      // if set, save the contract info meta
      if ($wpsc_network) update_post_meta($post_id, 'wpsc_network', $wpsc_network);
      if ($wpsc_txid) update_post_meta($post_id, 'wpsc_txid', $wpsc_txid);
      if ($wpsc_owner) update_post_meta($post_id, 'wpsc_owner', $wpsc_owner);
      if ($wpsc_contract_address) update_post_meta($post_id, 'wpsc_contract_address', $wpsc_contract_address);
      if ($wpsc_token_contract_address) update_post_meta($post_id, 'wpsc_token_contract_address', $wpsc_token_contract_address);
      if ($wpsc_factory) update_post_meta($post_id, 'wpsc_factory', $wpsc_factory);
      if ($wpsc_blockie) update_post_meta($post_id, 'wpsc_blockie', $wpsc_blockie);
      if ($wpsc_blockie_token) update_post_meta($post_id, 'wpsc_blockie_token', $wpsc_blockie_token);
      if ($wpsc_blockie_owner) update_post_meta($post_id, 'wpsc_blockie_owner', $wpsc_blockie_owner);
      if ($wpsc_qr_code) update_post_meta($post_id, 'wpsc_qr_code', $wpsc_qr_code);
      if ($wpsc_token_qr_code) update_post_meta($post_id, 'wpsc_token_qr_code', $wpsc_token_qr_code);

    }

  }

  public function wpscSmartContractSpecification() {

    wp_nonce_field( 'wpsc_repeatable_meta_box_nonce', 'wpsc_repeatable_meta_box_nonce' );

    $m = new Mustache_Engine;

    $args =  self::getMetaboxNFTArgs();

    echo $m->render(
      WPSC_Mustache::getTemplate('metabox-nft-collection'),
      $args
    );

  }

  static private function addRoleRow($wpsc_nft_roles, $key, $roles) {
    global $pagenow;
    if (
      (!empty($wpsc_nft_roles) and array_search($key, $wpsc_nft_roles)!==false) or
      ($pagenow=="post-new.php" and 
          in_array( $key, $roles ) // by default allow current role to create NFT
      ) 
    ) {
      return ["role"=>$key, "checked"=>true];
    } else {
      return ["role"=>$key];
    }  
  }

  static function getRoles($get_values=false) {
    global $wp_roles;

    $user = wp_get_current_user();

    $roles = (array) $user->roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    if ($get_values) {
      $wpsc_nft_roles = get_post_meta(get_the_ID(), 'wpsc_nft_roles', true);    
    }

    foreach ($editable_roles as $key => $value) {
      if ($get_values) {
        $res[] = self::addRoleRow($wpsc_nft_roles, $key, $roles);
      } else {
        $res[]=["role"=>$key];
      }
    }

    return $res;
  }

  static public function getMetaboxNFTArgs() {
    
    global $pagenow;

    $m = new Mustache_Engine;

    $id = get_the_ID();

    $wpsc_flavor = get_post_meta($id, 'wpsc_flavor', true);
    $wpsc_adv_hard = get_post_meta($id, 'wpsc_adv_hard', true);
    $wpsc_adv_cap = get_post_meta($id, 'wpsc_adv_cap', true);
    $wpsc_adv_white = get_post_meta($id, 'wpsc_adv_white', true);
    $wpsc_adv_pause = get_post_meta($id, 'wpsc_adv_pause', true);
    $wpsc_adv_timed = get_post_meta($id, 'wpsc_adv_timed', true);
    $wpsc_adv_pause_nft = get_post_meta($id, 'wpsc_adv_pause_nft', true);

    $wpsc_adv_opening = get_post_meta($id, 'wpsc_adv_opening', true);
    $wpsc_adv_closing = get_post_meta($id, 'wpsc_adv_closing', true);
    
    $wpsc_anyone_can_mint_tmp = get_post_meta($id, 'wpsc_anyone_can_mint', true);    

    if (!$wpsc_anyone_can_mint_tmp or $wpsc_anyone_can_mint_tmp=="false") {
      $wpsc_anyone_can_mint=false;
    } else {
      $wpsc_anyone_can_mint=true;
    }

    $wpsc_commission = get_post_meta($id, 'wpsc_commission', true);
    $wpsc_royalties = get_post_meta($id, 'wpsc_royalties', true);
    $wpsc_wallet = get_post_meta($id, 'wpsc_wallet', true);
    $wpsc_token = get_post_meta($id, 'wpsc_token', true);
    $wpsc_name = get_post_meta($id, 'wpsc_name', true);    
    $wpsc_symbol = get_post_meta($id, 'wpsc_symbol', true);    
    $wpsc_show_header = get_post_meta($id, 'wpsc_show_header', true);
    $wpsc_anyone_can_author = get_post_meta($id, 'wpsc_anyone_can_author', true);    
    $wpsc_show_breadcrumb = get_post_meta($id, 'wpsc_show_breadcrumb', true);    
    $wpsc_show_category = get_post_meta($id, 'wpsc_show_category', true);    
    $wpsc_show_id = get_post_meta($id, 'wpsc_show_id', true);    
    $wpsc_show_tags = get_post_meta($id, 'wpsc_show_tags', true);    
    $wpsc_show_owners = get_post_meta($id, 'wpsc_show_owners', true);    
    $wpsc_columns_n = get_post_meta($id, 'wpsc_columns_n', true);
    $wpsc_font_main_color = get_post_meta($id, 'wpsc_font_main_color', true);    
    $wpsc_font_secondary_color = get_post_meta($id, 'wpsc_font_secondary_color', true);    
    $wpsc_background_color = get_post_meta($id, 'wpsc_background_color', true);
    $wpsc_tag_bg_color = get_post_meta($id, 'wpsc_tag_bg_color', true);
    $wpsc_tag_color = get_post_meta($id, 'wpsc_tag_color', true);
    $wpsc_cat_bg_color = get_post_meta($id, 'wpsc_cat_bg_color', true);
    $wpsc_cat_color = get_post_meta($id, 'wpsc_cat_color', true);
    $wpsc_graph_bg_color = get_post_meta($id, 'wpsc_graph_bg_color', true);
    $wpsc_graph_line_color = get_post_meta($id, 'wpsc_graph_line_color', true);
    
    if ($pagenow=="post-new.php") {
      $wpsc_show_header = "on";
      $wpsc_show_breadcrumb = "on";
      $wpsc_show_category = "on";
      $wpsc_show_id = "on";
      $wpsc_show_tags = "on";
      $wpsc_show_owners = "on";
    }

    if (!$wpsc_font_main_color) {
      $wpsc_font_main_color="#000000";
    }
    if (!$wpsc_font_secondary_color) {
      $wpsc_font_secondary_color="#666666";
    }
    if (!$wpsc_background_color) {
      $wpsc_background_color="#FFFFFF";
    }

    if (!$wpsc_tag_bg_color) {
      $wpsc_tag_bg_color = "#cccccc";
    }
    if (!$wpsc_tag_color) {
      $wpsc_tag_color = "#655e5e";
    }
    if (!$wpsc_cat_bg_color) {
      $wpsc_cat_bg_color = "#00b5ad";
    }
    if (!$wpsc_cat_color) {
      $wpsc_cat_color = "#ffffff";
    }
    if (!$wpsc_graph_bg_color) {
      $wpsc_graph_bg_color = "#b3fef7";
    }
    if (!$wpsc_graph_line_color) {
      $wpsc_graph_line_color = "#07c2b2";
    }

    $args = [
      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' =>  __('Learn More', 'wp-smart-contracts'),
      'nft-standard' =>  __('Standard NFT Collection', 'wp-smart-contracts'),
      'nft-standard-desc' =>  __('A simple ERC-721 Standard Token. You can create and transfer collectibles.', 'wp-smart-contracts'),

      'nft-marketplace' =>  __('NFT Marketplace', 'wp-smart-contracts'),
      'nft-marketplace-desc' =>  __('A marketplace to buy & sell NFT.', 'wp-smart-contracts'),
      'crowdsale-with-tokens' =>  __('NFT with payments in Tokens', 'wp-smart-contracts'),
      'crowdsale-with-tokens-desc' =>  __('A Crowdsale that allows you to sell an existing token, and also receive payments in any ERC-20 Token', 'wp-smart-contracts'),
      'custom-token' =>  __('You can sell an existing token of yours'),
      'dynamic-cap' =>  __('The maximum cap will be determined by the number of tokens you approve to sell'),
      'payments-in-token' =>  __('You can receive contributions in ERC-20 tokens'),

      'custom-token-tooltip' =>  __('The rest of the NFT contracts create the token for you. This one works with existing tokens you own.'),
      'dynamic-cap-tooltip' =>  __('You sell only the tokens you approve to the NFT'),
      'payments-in-token-tooltip' =>  __('Your NFT can sell tokens in Ether and in ERC-20 Tokens'),

      'flavor' =>  __('Flavor', 'wp-smart-contracts'),
      'nft-spec' =>  __('NFT Specification', 'wp-smart-contracts'), 
      'nft-spec-desc' =>  __('Non Fungible Tokens Smart Contract including a Marketplace to Buy&Sell and Auction system.', 'wp-smart-contracts'),
      'features' =>  __('Features', 'wp-smart-contracts'),

      'nft-marketplace-auction' =>  __('NFT Marketplace', 'wp-smart-contracts'),
      'nft-marketplace-auction-desc' =>  __('Fully featured NFT Marketplace', 'wp-smart-contracts'),

      'nft-marketplace-token' =>  __('NFT Token Marketplace with Royalties', 'wp-smart-contracts'),
      'nft-marketplace-token-desc' =>  __('Fully featured NFT ERC-20 / BEP20 Token Marketplace with auction, selling and royalties.', 'wp-smart-contracts'),

      "wpsc_flavor" => $wpsc_flavor,
      "wpsc_adv_hard" => $wpsc_adv_hard,
      "wpsc_adv_cap" => $wpsc_adv_cap,
      "wpsc_adv_white" => $wpsc_adv_white,
      "wpsc_adv_pause" => $wpsc_adv_pause,
      "wpsc_adv_timed" => $wpsc_adv_timed,
      "wpsc_adv_pause_nft" => $wpsc_adv_pause_nft,

      'img-custom' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/custom-card.png',
      'erc-20-custom' => __('Looking for something else?', 'wp-smart-contracts'),
      'custom-message' => __('If you need to create a smart contract with custom features we can help', 'wp-smart-contracts'),
      'contact-us' => __('Contact us', 'wp-smart-contracts'),

      "wpsc_anyone_can_mint" => $wpsc_anyone_can_mint,
      "wpsc_commission" => $wpsc_commission,
      "wpsc_royalties" => $wpsc_royalties,
      "wpsc_wallet" => $wpsc_wallet,
      "wpsc_token" => $wpsc_token,
      "wpsc_name" => $wpsc_name,
      "wpsc_symbol" => $wpsc_symbol,
      "wpsc_show_header" => $wpsc_show_header,
      "wpsc_anyone_can_author" => $wpsc_anyone_can_author,
      "wpsc_show_breadcrumb" => $wpsc_show_breadcrumb,
      "wpsc_show_category" => $wpsc_show_category,
      "wpsc_show_id" => $wpsc_show_id,
      "wpsc_show_tags" => $wpsc_show_tags,
      "wpsc_show_owners" => $wpsc_show_owners,
      "wpsc_columns_n" => $wpsc_columns_n,
      "wpsc_font_main_color" => $wpsc_font_main_color,
      "wpsc_tag_bg_color" => $wpsc_tag_bg_color,
      "wpsc_tag_color" => $wpsc_tag_color,
      "wpsc_cat_bg_color" => $wpsc_cat_bg_color,
      "wpsc_cat_color" => $wpsc_cat_color,
      "wpsc_graph_bg_color" => $wpsc_graph_bg_color,
      "wpsc_graph_line_color" => $wpsc_graph_line_color,
      "wpsc_font_secondary_color" => $wpsc_font_secondary_color,
      "wpsc_background_color" => $wpsc_background_color,

      "wpsc-skin-0" => plugins_url( "assets/img/skin0.png", dirname(__FILE__) ),
      "wpsc-skin-1" => plugins_url( "assets/img/skin1.png", dirname(__FILE__) ),
      "wpsc-skin-2" => plugins_url( "assets/img/skin1.png", dirname(__FILE__) ),
      "wpsc-skin-3" => plugins_url( "assets/img/skin1.png", dirname(__FILE__) ),

      'ownable-nft' =>  __('Individual owners can hold unique items', 'wp-smart-contracts'),
      'ownable-nft-tooltip' => __('Non Fungible Tokens (NFT) are ownable by only one user', 'wp-smart-contracts'),

      'transferable-nft' =>  __('Owners can transfer NFT to any account', 'wp-smart-contracts'),
      'transferable-nft-tooltip' => __('Accounts owning an item can transfer them to any other account', 'wp-smart-contracts'),

      'nft-mintable' =>  __('Authorized accounts can create (mint) new items', 'wp-smart-contracts'),
      'nft-mintable-tooltip' => __('Depending on the setting only contract owners or anyone can mint new NFT items', 'wp-smart-contracts'),

      'nft-metadata' =>  __('Metadata support for name, symbol and attributes', 'wp-smart-contracts'),
      'nft-metadata-tooltip' => __('The metadata extension includes name, symbol and a TokenURI with all the attributes of the NFT', 'wp-smart-contracts'),

      'nft-enumerable' =>  __('Enumerable support. Your NFTs are discoverable', 'wp-smart-contracts'),
      'nft-enumerable-tooltip' => __('This allows your contract to publish its full list of NFTs', 'wp-smart-contracts'),

      'nft-media' =>  __('Image and video support for NFT', 'wp-smart-contracts'),
      'nft-media-tooltip' => __('You can add images or videos as NFTs', 'wp-smart-contracts'),

      'nft-buy-sell' =>  __('Buy and Sell support', 'wp-smart-contracts'),
      'nft-buy-sell-tooltip' => __('Owners of NFT can sell their tokens in the Marketplace, and any interested user can buy', 'wp-smart-contracts'),

      'nft-burn' =>  __('Burn support', 'wp-smart-contracts'),
      'nft-burn-tooltip' => __('Owners of NFT can burn their NFT items', 'wp-smart-contracts'),
      
      'nft-auction' =>  __('Auctions supported', 'wp-smart-contracts'),
      'nft-auction-tooltip' => __('Owners of NFT can auction their NFTs in the Marketplace, and any interested user can buy', 'wp-smart-contracts'),

      'nft-buy-native' =>  __('Sales and auctions are done in Ether or Blockchain native coin', 'wp-smart-contracts'),
      'nft-buy-native-tooltip' => __('Ether, BNB, xDai and Matic is supported for corresponding Blockchains', 'wp-smart-contracts'),

      'nft-buy-token' =>  __('Sales and auctions are done in any ERC-20 or BEP20 Standard token defined', 'wp-smart-contracts'),
      'nft-buy-token-tooltip' => __('The payments are done with a predefined token defined by the smart contract creator. Only one token is allowed.', 'wp-smart-contracts'),

      'nft-royalties' =>  __('Supports the ability to distribute royalties to the creators from resales', 'wp-smart-contracts'),
      'nft-royalties-tooltip' => __('A predefined percentage goes to creators on every sale', 'wp-smart-contracts'),

      'nft-vanities' =>  __('Support attributes and categories', 'wp-smart-contracts'),
      'nft-vanities-tooltip' => __('Include tags and categories', 'wp-smart-contracts'),

      'marketplace-options' =>  __('Smart Contracts Options', 'wp-smart-contracts'),
      'marketplace-options-desc' =>  __('', 'wp-smart-contracts'),
      
      'marketplace-security' =>  __('Security Options', 'wp-smart-contracts'),
      'anyone-can-author-any-nft' => __('All users can edit all NFT', 'wp-smart-contracts'),
      'anyone-can-author-any-nft-tooltip' =>  $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("By default NFT Items can be modified by their authors, but if you like all authors to be able to modify all NFT Items activate this option.", 'wp-smart-contracts')]),
      'anyone-can-author-any-nft-desc' => __('This is a front-end setting to allow all creators to modify the data of all items (not recommended)', 'wp-smart-contracts'),

      'name' =>  __('Name', 'wp-smart-contracts'),
      'name-desc' =>  __('The name of the collection', 'wp-smart-contracts'),
      'name-desc-tooltip' =>  $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Just like ERC-20 has symbol and names, ERC-721 tokens has a symbol and name as well", 'wp-smart-contracts')]),

      'symbol' =>  __('Symbol', 'wp-smart-contracts'),
      'symbol-desc' =>  __('The symbol of the collection. Keep it short - e.g. "HIX"', 'wp-smart-contracts'),
      'symbol-desc-tooltip' =>  $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Just like ERC-20 has symbol and names, ERC-721 tokens has a symbol and name as well", 'wp-smart-contracts')]),

      'anyone-can-mint' => __('Who can mint?', 'wp-smart-contracts'),
      'anyone-can-mint-desc' => __('This is a Smart Contract setting. Anyone or only owner can mint?', 'wp-smart-contracts'),
      'anyone-can-mint-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("If anyone can mint means that anyone can create NFT items in your Marketplace. Otherwise only the contract owner can mint new NFT items.", 'wp-smart-contracts')]),

      'roles' => self::getRoles(true),
      'anyone-can-mint-wp' => __('What User Roles can create NFT Items in your Marketplace?', 'wp-smart-contracts'),
      'anyone-can-mint-desc-wp' => __('This is a WordPress level setting. Choose authorized WP User Roles to create NFT Items', 'wp-smart-contracts'),
      'anyone-can-mint-tooltip-wp' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Your users are required to register on your website before creating NFT Items.", 'wp-smart-contracts')]),

      'royalties' => __('Royalty percentage for creators', 'wp-smart-contracts'),
      'royalties-desc' => __('Percentage royalty, ranging from 0 to 100', 'wp-smart-contracts'),
      'royalties-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Royalties to the creators from resales. 0 means no commission, 100 means 100% of the sale as commission.", 'wp-smart-contracts')]),

      'commission' => __('Sales and auctions commissions', 'wp-smart-contracts'),
      'commission-desc' => __('Percentage commission, ranging from 0 to 100', 'wp-smart-contracts'),
      'commission-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => __("Commission that you are going to get from Sales and Auctions. 0 means no commission, 100 means 100% of the sale as commission.", 'wp-smart-contracts')]),

      'wallet' =>  __('Wallet', 'wp-smart-contracts'),
      'wallet-desc' =>  __('Ethereum address or EVM compatible wallet address to receive funds', 'wp-smart-contracts'),
      'wallet-desc-tooltip' =>  __('The beneficiary account that will receive the Marketplace commissions in Ether, BNB, xDai or Matic', 'wp-smart-contracts'),

      'token' =>  __('Token for payments', 'wp-smart-contracts'),
      'token-desc' =>  __('Standard ERC-20 or BEP20 token to be used for payment of sales and auctions', 'wp-smart-contracts'),
      'token-desc-tooltip' =>  __('Token used for all payments, commissions and royalties', 'wp-smart-contracts'),

      'nft-options' =>  __('NFT Options', 'wp-smart-contracts'),
      'nft-options-desc' =>  __('What type of media do you want to include in your collectible?', 'wp-smart-contracts'),

      'graph-bg-color' => __('Graph background color', 'wp-smart-contracts'),
      'graph-bg-color-desc' => __('Graph background color for the price history of NFT ', 'wp-smart-contracts'),
      'graph-bg-color-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => 
        "<p>".__('Graph background color ', 'wp-smart-contracts')."</p>"."<img src=\"".dirname(plugin_dir_url( __FILE__ )) . '/assets/img/nft-color-8.jpg' ."\" class=\"nft-help-img\">"
      ]),

      'graph-line-color' => __('Graph line color', 'wp-smart-contracts'),
      'graph-line-color-desc' => __('Graph line color for the price history of NFT ', 'wp-smart-contracts'),
      'graph-line-color-tooltip' => $m->render(WPSC_Mustache::getTemplate('tooltip'), ['tip' => 
        "<p>".__('Graph line color ', 'wp-smart-contracts')."</p>"."<img src=\"".dirname(plugin_dir_url( __FILE__ )) . '/assets/img/nft-color-9.jpg' ."\" class=\"nft-help-img\">"
      ]),

      'nft-sections-help' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/nft-sections.png',

      'img-matcha' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/matcha-card.png',
      'img-mochi' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/mochi-card.png',
      'img-suika' => dirname(plugin_dir_url( __FILE__ )) . '/assets/img/suika-card.png',

      'wp-admin-url' => get_admin_url()
    ];

    if ($wpsc_columns_n==1) $args["wpsc_columns_n_1"] = true;
    if ($wpsc_columns_n==2) $args["wpsc_columns_n_2"] = true;
    if ($wpsc_columns_n==3 or !$wpsc_columns_n) $args["wpsc_columns_n_3"] = true;
    if ($wpsc_columns_n==4) $args["wpsc_columns_n_4"] = true;

    if ($wpsc_flavor=="mochi") $args["is-mochi"] = true;
    if ($wpsc_flavor=="matcha") $args["is-matcha"] = true;
    if ($wpsc_flavor=="suika") $args["is-suika"] = true;

    $wpsc_contract_address = get_post_meta($id, 'wpsc_contract_address', true);

    // show contract definition
    if ($wpsc_contract_address) {
      $args["readonly"] = true;
    }

    return $args;

  }

  static public function getNetworkInfo($wpsc_network) {

    if ($wpsc_network and $arr = WPSC_helpers::getNetworks()) {

      return [
        $arr[$wpsc_network]["color"],
        $arr[$wpsc_network]["nftn"],
        $arr[$wpsc_network]["url2"],
        __($arr[$wpsc_network]["title"], 'wp-smart-contracts')
      ];

    }

    return ["", "", "", ""];

  }

  public function wpscSmartContract() {

    global $pagenow;

    $id = get_the_ID();

    $wpsc_network = get_post_meta($id, 'wpsc_network', true);
    $wpsc_txid = get_post_meta($id, 'wpsc_txid', true);
    $wpsc_owner = get_post_meta($id, 'wpsc_owner', true);
    $wpsc_contract_address = get_post_meta($id, 'wpsc_contract_address', true);
    $wpsc_token_contract_address = get_post_meta($id, 'wpsc_token_contract_address', true);
    $wpsc_blockie = get_post_meta($id, 'wpsc_blockie', true);
    $wpsc_blockie_owner = get_post_meta($id, 'wpsc_blockie_owner', true);
    $wpsc_qr_code = get_post_meta($id, 'wpsc_qr_code', true);
    $token_id = get_post_meta($id, 'token_id', true);
    $wpsc_token_qr_code = get_post_meta($id, 'wpsc_token_qr_code', true);

    list($color, $nftn, $etherscan, $network_val) = WPSC_Metabox::getNetworkInfo($wpsc_network);

    $m = new Mustache_Engine;

    // show contract
    if ($wpsc_contract_address) {

      $wpsc_flavor          = get_post_meta($id, 'wpsc_flavor', true);
      $wpsc_symbol          = get_post_meta($id, 'wpsc_symbol', true);
      $wpsc_name            = get_post_meta($id, 'wpsc_name', true);
      $wpsc_commission      = get_post_meta($id, 'wpsc_commission', true);
      $wpsc_royalties       = get_post_meta($id, 'wpsc_royalties', true);
      $wpsc_wallet          = get_post_meta($id, 'wpsc_wallet', true);
      $wpsc_token           = get_post_meta($id, 'wpsc_token', true);
      $wpsc_anyone_can_mint_temp = get_post_meta($id, 'wpsc_anyone_can_mint', true);

      if (!$wpsc_anyone_can_mint_temp or $wpsc_anyone_can_mint_temp==="false") {
        $wpsc_anyone_can_mint = false;
      } elseif ($wpsc_anyone_can_mint_temp) {
        $wpsc_anyone_can_mint = true;
      }

      if ($wpsc_flavor=="mochi") $the_color = "purple";
      else if ($wpsc_flavor=="matcha") $the_color = "green";
      else if ($wpsc_flavor=="suika") $the_color = "red";
      
      $nftInfo = [
          "type" => $wpsc_flavor,
          "wpsc_symbol" => $wpsc_symbol,
          "wpsc_name" => $wpsc_name,
          "wpsc_commission" => $wpsc_commission,
          "wpsc_royalties" => $wpsc_royalties,
          "wpsc_wallet" => $wpsc_wallet,
          "wpsc_token" => $wpsc_token,
          "wpsc_anyone_can_mint" => $wpsc_anyone_can_mint,
          "symbol_label" => __("Symbol", "wp-smart-contracts"),
          "name_label" => __("Name", "wp-smart-contracts"),
          "commission_label" => __("Commission", "wp-smart-contracts"),
          "royalties_label" => __("Royalty", "wp-smart-contracts"),
          "anyone_label" => __("Anyone can mint", "wp-smart-contracts"),
          "wallet_label" => __("Wallet", "wp-smart-contracts"),
          "token_label" => __("Token", "wp-smart-contracts"),
          "color" => $the_color
      ];

      $nftInfo["imgUrl"] = dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/';

      $atts = [
          'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
          'learn-more' => __('Learn More', 'wp-smart-contracts'),
          'smart-contract-desc' => __('Go live with your NFT. You can publish your NFT in any available network.', 'wp-smart-contracts'),
          'nft-deployed-smart-contract' => __('NFT Smart Contract', 'wp-smart-contracts'),
          'token-deployed-smart-contract' => __('Token Smart Contract', 'wp-smart-contracts'),
          'ethereum-network' => $network_val,
          'ethereum-color' => $color,
          'ethereum-nftn' => $nftn,
          'contract-address' => $wpsc_contract_address,
          'etherscan' => $etherscan,
          'contract-address-text' => __('Contract Address', 'wp-smart-contracts'),
          'contract-address-desc' => __('The Smart Contract Address of your nft', 'wp-smart-contracts'),
          'txid-text' => __('Transaction ID', 'wp-smart-contracts'),
          'owner-text' => __('Owner Account', 'wp-smart-contracts'),

          'blockie' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie]),
          'blockie-token' => isset($wpsc_blockie_token) ? $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie_token]): "",
          'blockie-owner' => $m->render(WPSC_Mustache::getTemplate('blockies'), ['blockie' => $wpsc_blockie_owner]),
          'nft-info-nft' => $m->render(WPSC_Mustache::getTemplate('nft-info'), $nftInfo),
          'txid' => $wpsc_txid,
          'txid-short' => WPSC_helpers::shortify($wpsc_txid),
          'owner' => $wpsc_owner,
          'owner-short' => WPSC_helpers::shortify($wpsc_owner),
          'wpsc_anyone_can_mint' => get_post_meta($id, 'wpsc_anyone_can_mint', true),
          'wpsc_commission' => get_post_meta($id, 'wpsc_commission', true),
          'wpsc_royalties' => get_post_meta($id, 'wpsc_royalties', true),
          'wpsc_wallet' => get_post_meta($id, 'wpsc_wallet', true),
          'wpsc_token' => get_post_meta($id, 'wpsc_token', true),
          'wpsc_name' => get_post_meta($id, 'wpsc_name', true),
          'wpsc_symbol' => get_post_meta($id, 'wpsc_symbol', true),
          'wpsc_show_header' => get_post_meta($id, 'wpsc_show_header', true),
          'wpsc_anyone_can_author' => get_post_meta($id, 'wpsc_anyone_can_author', true),
          'wpsc_show_breadcrumb' => get_post_meta($id, 'wpsc_show_breadcrumb', true),
          'wpsc_show_category' => get_post_meta($id, 'wpsc_show_category', true),
          'wpsc_show_id' => get_post_meta($id, 'wpsc_show_id', true),
          'wpsc_show_tags' => get_post_meta($id, 'wpsc_show_tags', true),
          'wpsc_show_owners' => get_post_meta($id, 'wpsc_show_owners', true),
          'wpsc_columns_n' => get_post_meta($id, 'wpsc_columns_n', true),
          'wpsc_font_main_color' => get_post_meta($id, 'wpsc_font_main_color', true),
          "wpsc_tag_bg_color" => get_post_meta($id, "wpsc_tag_bg_color", true),
          "wpsc_tag_color" => get_post_meta($id, "wpsc_tag_color", true),
          "wpsc_cat_bg_color" => get_post_meta($id, "wpsc_cat_bg_color", true),
          "wpsc_cat_color" => get_post_meta($id, "wpsc_cat_color", true),
          "wpsc_graph_bg_color" => get_post_meta($id, "wpsc_graph_bg_color", true),
          "wpsc_graph_line_color" => get_post_meta($id, "wpsc_graph_line_color", true),
          'wpsc_font_secondary_color' => get_post_meta($id, 'wpsc_font_secondary_color', true),
          'wpsc_background_color' => get_post_meta($id, 'wpsc_background_color', true),

          'qr-code-data' => $wpsc_qr_code,
          'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
          'connect-to-metamask' => __('Connect to Metamask', 'wp-smart-contracts'),

      ];

      if ($wpsc_txid) {
          $atts["txid_exists"] = true;
      }

      if ($wpsc_flavor=="mochi") $atts["is-mochi"] = true;
      if ($wpsc_flavor=="matcha") $atts["is-matcha"] = true;
      if ($wpsc_flavor=="suika") $atts["is-suika"] = true;

      echo $m->render(WPSC_Mustache::getTemplate('metabox-smart-contract-nft-collection'), $atts);

    // show buttons to load or create a contract
    } else {

      echo $m->render(
          WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-nft-collection'),
          self::getSmartContractButtons()
      );

    }

  }

  static public function getSmartContractButtons($show_load=true) {

    $m = new Mustache_Engine;

    return [
      'smart-contract' => __('Smart Contract', 'wp-smart-contracts'),
      'learn-more' => __('Learn More', 'wp-smart-contracts'),
      'smart-contract-desc' => __('Go live with your NFT. You can publish your NFT in any available network', 'wp-smart-contracts'),
      'new-smart-contract' => __('New Smart Contract', 'wp-smart-contracts'),
      'text' => __('To deploy your Smart Contracts you need to be connected to a Network. Please connect to Metamask and choose the right network to continue.', 'wp-smart-contracts'),
      'fox' => plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) ),
      'connect-to-metamask' => __('Connect to Metamask', 'wp-smart-contracts'),
      'deploy-with-wpst' => $m->render(
        WPSC_Mustache::getTemplate('metabox-smart-contract-buttons-wpst'),
        [
          'show_load' => $show_load,
          'load' => __('Load', 'wp-smart-contracts'),
          'load-desc' => __('Load an existing Smart Contract', 'wp-smart-contracts'),
          'deploy' => __('Deploy', 'wp-smart-contracts'),
          'deploy-desc' => __('Deploy your Smart Contract to the Blockchain using Ether', 'wp-smart-contracts'),
          'deploy-desc-wpic-disabled' => __('WPIC Deployment for this flavor is deactivated until March 1, 2022.', 'wp-smart-contracts'),
          'deploy-desc-token' => __('Deploy your Smart Contract to the Blockchain using WPIC is a two step process:', 'wp-smart-contracts'),
          'deploy-desc-token-1' => __('First you need to authorize the factory to use the WPIC funds', 'wp-smart-contracts'),
          'deploy-desc-token-2' => __('Then you can deploy your contract using WPIC', 'wp-smart-contracts'),
          'no-wpst' => __('No WPIC found', 'wp-smart-contracts'),
          'not-enough-wpst' => __('Not enough WPIC found', 'wp-smart-contracts'),
          'authorize' => __('Authorize', 'wp-smart-contracts'),
          'authorize-complete' => __('Authorization was successful, click "Deploy" to proceed', 'wp-smart-contracts'),
          'deploy-token' => __('Deploy using WP Ice Cream (WPIC)', 'wp-smart-contracts'),
          'deploy-token-image' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/wp-smart-token.png',
          'deploy-using-ether' => __('Deploy to the selected network', 'wp-smart-contracts'),
          'do-you-have-an-erc20-address' => __('Do you already have a Matcha contract address?', 'wp-smart-contracts'),
          'wpst-balance' => __('WPIC Balance', 'wp-smart-contracts'),
          'button-id' => "wpsc-deploy-contract-button-nft",
          'authorize-button-id' => "wpsc-deploy-contract-button-wpst-authorize-nft",
          'deploy-button-wpst' => "wpsc-deploy-contract-button-wpst-deploy-nft",
        ]
      ),
      'ethereum-address' => __('Ethereum Network Contract Address', 'wp-smart-contracts'),
      'ethereum-address-desc' => __('Please fill out the contract address you want to import', 'wp-smart-contracts'),
      'ethereum-address-important' => __('Important', 'wp-smart-contracts'),
      'ethereum-address-important-message' => __('Keep in mind that the contract is going to be loaded using the current network and current account as owner', 'wp-smart-contracts'),
      'active-net-account' => __('Currently active Ethereum Network and account:', 'wp-smart-contracts'),
      'smart-contract-address' => __('Smart Contract Address'),
      'load' => __('Load', 'wp-smart-contracts'),
      'ethereum-deploy' => __('Network Deploy', 'wp-smart-contracts'),
      'ethereum-deploy-desc' => __('Are you ready to deploy your NFT to the currently active Ethereum Network?', 'wp-smart-contracts'),
      'cancel' => __('Cancel', 'wp-smart-contracts'),
      'yes-proceed' => __('Yes, please proceed', 'wp-smart-contracts'),
      'deployed-smart-contract' => __('Deployed Smart Contract', 'wp-smart-contracts'),
    ];

  }

  public function wpscSourceCode() {

      // load the contract technical atts
      $atts = WPSC_Metabox::wpscGetMetaSourceCodeAtts();

      if (!empty($atts)) {

        $m = new Mustache_Engine;
        echo $m->render(
          WPSC_Mustache::getTemplate('metabox-source-code'),
          $atts
        );

      }

  }

  // with great powers... 
  public static function wpscReminder() {
    echo WPSC_Metabox::wpscReminder();
  }

  public function wpscSidebar() {

    $m = new Mustache_Engine;
    echo $m->render(
      WPSC_Mustache::getTemplate('metabox-sidebar-nft'),
      [
        'white-logo' => dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/wpsc-white-logo.png',
        'tutorials-tools' => __('Tutorials', 'wp-smart-contracts'),
        'tutorials-tools-desc' => __('Here you can find a few tutorials that might be useful to deploy, test and use your Smart Contracts.', 'wp-smart-contracts'),
        'screencasts' => __('Screencasts'),
        'deploy' => __('NFT Marketplace Documentation'),
        'wpic_info' => WPSC_helpers::renderWPICInfo(),
        'learn-more' => __('Learn More', 'wp-smart-contracts'),
        'docs' => __('Documentation', 'wp-smart-contracts'),
        'doc' => "https://wpsmartcontracts.com/doc-nft.php",
        'wpsc-logo' => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png',

        'switch-networks' => __('Ethereum fees too expensive?', 'wp-smart-contracts'),
        'learn-how-to-get-wpst' => __('Learn how to get WPIC', 'wp-smart-contracts'),
        'learn-how-to-get-ether' => __('Learn how to get Ether', 'wp-smart-contracts'),
        'learn-how-to-get-coins' => __('Learn how to get coins for other blockchains', 'wp-smart-contracts'),
        'switch-explain' => __('Deploy your contracts to Layer 2 Solutions', 'wp-smart-contracts'),
        'switch-explain-2' => __('Deploy your contracts with lower fees', 'wp-smart-contracts'),
        'switch-explain-3' => __('You can use differente blockchains, like Binance Smart Chain, xDai or Polygon (Matic)', 'wp-smart-contracts'),
        'switch-explain-4' => __('Choose the network below and click on Switch button', 'wp-smart-contracts'),
        'help' => $m->render(
          WPSC_Mustache::getTemplate('metabox-nft-help'),
          [
            'need-help' => __('Need help?', 'wp-smart-contracts'),
            'need-help-desc' => __('Deploy your NFT contract to Ethereum', 'wp-smart-contracts'),
            'deploy-ethereum' => __('How to deploy a NFT Marketplace to Ethereum', 'wp-smart-contracts'),
            'need-help-desc2' => __('Deploy your NFT contract to Binance Smart Chain', 'wp-smart-contracts'),
            'deploy-bsc' => __('How to deploy a NFT Marketplace to Binance Smart Chain', 'wp-smart-contracts'),
            'screencast' => __('Screencast', 'wp-smart-contracts'),
          ]
        )

      ]
    );

  }

}
