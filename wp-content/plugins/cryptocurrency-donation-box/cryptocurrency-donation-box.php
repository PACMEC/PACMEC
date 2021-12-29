<?php
/**
 * Plugin Name:Cryptocurrency Donation Box - Bitcoin & Crypto Donations
 * Description:Create cryptocurrency donation box and accept crypto coin payments, show coin address and QR code - best bitcoin & crypto donations plugin.
 * Plugin URI:https://coolplugins.net/
 * Author:Cool Plugins
 * Author URI:https://coolplugins.net/
 * Version: 1.7
 * License: GPL2
 * Text Domain:CDBBC
 * Domain Path: /languages
 *
 * @package Cryptocurrency_Donation_Box
 */

/*
Copyright (C) 2018  CoolPlugins contact@coolplugins.net

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('CDBBC_VERSION')) {
    return;
}

define('CDBBC_VERSION', '1.7');
define('CDBBC_FILE', __FILE__);
define('CDBBC_PATH', plugin_dir_path(CDBBC_FILE));
define('CDBBC_URL', plugin_dir_url(CDBBC_FILE));

register_activation_hook(CDBBC_FILE, array('Cryptocurrency_Donation_Box', 'activate'));
register_deactivation_hook(CDBBC_FILE, array('Cryptocurrency_Donation_Box', 'deactivate'));

/**
 * Class Cryptocurrency_Donation_Box
 */
final class Cryptocurrency_Donation_Box
{

    /**
     * Plugin instance.
     *
     * @var Cryptocurrency_Donation_Box
     * @access private
     */
    private static $instance = null;

    /**
     * Get plugin instance.
     *
     * @return Cryptocurrency_Donation_Box
     * @static
     */
    public static function get_instance()


    
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @access private
     */
    private function __construct()
    {
     
        $this->cdbbc_includes();
        add_action ('admin_init', array($this, 'CDBBC_do_activation_redirect'));
        add_shortcode('crypto-donation-box', array($this, 'crypto_donation_box_shortcode'));
        add_action( 'plugins_loaded',array($this,'cdbbc_load_lang'));
        if(is_admin()){    
            
            require_once CDBBC_PATH . 'admin/addon-dashboard-page/addon-dashboard-page.php';
            cool_plugins_crypto_addon_settings_page('crypto','cool-crypto-plugins','Cryptocurrency plugins by CoolPlugins.net', 'Crypto Plugins', 'dashicons-chart-area');

            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'cdbbc_setting_panel_action_link'));
            require_once CDBBC_PATH . '/admin/feedback/admin-feedback-form.php';   
            require_once CDBBC_PATH . '/includes/cdb-feedback-notice.php';
            new cdbbcFeedbackNotice();        
            // notice for review
		
            
        }
    }
   

    /**
     * Load plugin function 
     */
    public function cdbbc_includes()
    {
        require_once dirname(__FILE__) . '/includes/functions.php';
        require_once dirname(__FILE__) . '/includes/class.settings-api.php';
        require_once dirname(__FILE__) . '/includes/settings.php';    
        new CDB_Settings();       
    }

    /*
        donation box shortcode
    */

    function crypto_donation_box_shortcode($atts){
         $a = shortcode_atts(array(
        'id' => 'something',
        'class' => 'something else',
        'type' => '',
        ), $atts);
         $output='';
         $coin_tabs='';
         $coin_links='';
         $list_view='';
         $classic_list='';
         $tagvl=__('_tag','CDBBC');
         $i=0;
         $active_tab='';
         $design_type= $a['type'];
         $cdb_meta_button = CDBBC_URL .'assets/images/5_pay_mm_over.img';
         wp_enqueue_style('cbd-styles', CDBBC_URL . 'assets/css/cbd-styles.css');
         wp_enqueue_script('cbd-copy-clipboard', 'https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js',array('jquery'),true ); 
         wp_enqueue_script('cbd-metamask-script', CDBBC_URL . 'assets/js/cbd-metamask.js',array('jquery'),true );   
         $main_title=  CDBBC_get_option('main-title', 'coolplugins_advanced');
         $metamask_title = CDBBC_get_option('maintitle-metamask','coolplugins_advanced');
         $metamask_desc = CDBBC_get_option('cdb-metamask-desc', 'coolplugins_advanced');
         $metamask_tag = CDBBC_get_option('metamask_sp_tag', 'coolplugins_advanced');
         $metamask_description = !empty($metamask_desc) ? $metamask_desc :'Donate ETH Via PAY With Metamask';
         $cdb_metamask_title = !empty($metamask_title)? $metamask_title: 'Donate With MetaMask';   
         $desc = CDBBC_get_option('cdb-desc', 'coolplugins_advanced');
         $title=!empty($main_title)? $main_title: 'Donate [coin-name] to this address';
         $description = !empty($desc) ? $desc :'Scan the QR code or copy the address below into your wallet to send some [coin-name]';
         $all_coin_wall_add0= CDBBC_get_option_arr('wallet_addresses');
         $all_coin_wall_add1 = CDBBC_get_option_arr('wallet_addresses1');
        $all_coin_list1=!empty($all_coin_wall_add0)?$all_coin_wall_add0:[];
         $all_coin_list2 = !empty($all_coin_wall_add1)?$all_coin_wall_add1:[];         
         $all_coin_wall_add=array_merge($all_coin_list1,$all_coin_list2);
         $metamask_wall_add = isset($all_coin_wall_add['metamask'])?$all_coin_wall_add['metamask']:'';

        if($design_type!='metamask'){
            if (!empty($all_coin_wall_add) && is_array($all_coin_wall_add) &&  array_filter($all_coin_wall_add) ) {  
                foreach( $all_coin_wall_add as $id=>$address){                     
                    if(!empty($address)&& $address!==false  && (strpos($id, "_tag") == false)) {

                        if($i==0){
                            $active_tab='current';
                        }else{
                            $active_tab ='';
                        }                    
                        if($id!='metamask'){
                            $coin_name=ucfirst(str_replace('-',' ',$id));
                        }
                        else{                      
                            $coin_name='MetaMask';  
                        }  

                        $title_content=str_replace('[coin-name]',$coin_name, $title);
                        $desc_content = str_replace('[coin-name]', $coin_name, $description);
                        $coin_logo=CDBBC_URL .'assets/logos/'.$id.'.svg';
                        $logo_html='<img src="'.$coin_logo.'"> ';
                        $logo_html.=$coin_name;                    
                        $coin_links .= '<li class="cbd-coins '.$active_tab.'" id="'.$id .'" data-tab="' . $id . '-tab">'.$logo_html.'</li>';                    
                        
                        if($design_type=='popup'){ 
                            if(($id!='metamask')){                  
                                $list_view .='<li class="cbd-list-items"><a class="cbd-list-popup" href="#donate'.$id.'" rel="modal:open"><div class="cdb-list-img"><img src="'.$coin_logo.'"></div><div class="cdb-list-content"><span class="cdb-list-donate-txt">'.__("Donate with","CDBBC").'</span><span class="cdb-list-coin">'.$coin_name.'</span></div></a></li>';
                                $list_view .='
                                <div id="donate'.$id.'" class="modal">
                                <div class="cbd-main-title">
                                <h2 class="cbd-title">Donate '.$coin_name.'</h2>
                                </div>
                                <div class="cbd-modal-body">
                                <div class="cbd-address">
                                <input type="text" class="wallet-address-input"  id="' . $id . '-list-wallet-address" name="' . $id . '-list-wallet-address" value="' . $address . '">';                
                                $list_view .= '
                                <button class="CDBBC_btn" data-clipboard-target="#' . $id . '-list-wallet-address">
                                '.__('COPY','CDBBC').'</button>
                    
                                </div>
                                <div class="cbd_qr_code">
                                <img src="https://chart.googleapis.com/chart?cht=qr&chl='.$address.'&chs=260x260&chld=L|0" alt="Scan to Donate '. $coin_name .' to '. $address .'"/>
                                </div>';
                                if(isset($all_coin_wall_add[$id.$tagvl]) && !empty($all_coin_wall_add[$id.$tagvl])){
                                    $list_view .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';
                                }
                                $list_view .=  
                                '</div>
                                </div>';
                            }
                            else{                            
                                $list_view .='<li class="cbd-list-items"><a class="cbd-list-popup" href="#donate'.$id.'" rel="modal:open"><div class="cdb-list-img"><img src="'.$coin_logo.'"></div><div class="cdb-list-content"><span class="cdb-list-donate-txt">'.__("Donate with","CDBBC").'</span><span class="cdb-list-coin">MetaMask</span></div></a></li>';
                                $list_view .='
                                <div id="donate'.$id.'" class="modal">';
                                $list_view .='<div class="cdb-metamask-wrapper" >
                                <h2 class="CDBBC-title">'. $cdb_metamask_title . '</h2>';
                                $list_view .= '<div id="tip-button" class="tip-button" data-metamask-address="'.$metamask_wall_add.'"></div>';
                                if(isset($all_coin_wall_add[$id.$tagvl])  && !empty($all_coin_wall_add[$id.$tagvl])){
                                $list_view .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';
                                }
                                $list_view .=   ' <div class="message"></div></div></div>';                        

                            }
                        }                    
                        elseif($design_type=='list'){
                            if($id!='metamask'){
                                $classic_list .= '<li class="cbd-classic-list">';
                                $classic_list .= '<h2 class="CDBBC-title">'. $title_content . '</h2>';
                                $classic_list.='<div class="CDBBC_qr_code"><img src="https://chart.googleapis.com/chart?cht=qr&chl='.$address.'&chs=260x260&chld=L|0" alt="Scan to Donate '. $coin_name .' to '. $address .'"/>';
                                $classic_list .= '</div><div class="CDBBC_classic_input_add">                    
                                <input type="text" class="wallet-address-input"  id="' . $id . '-classic-wallet-address" name="' . $id . '-classic-wallet-address" value="' . $address . '">';
                                $classic_list .= '<button class="CDBBC_btn" data-clipboard-target="#' . $id . '-classic-wallet-address">
                                '.__('COPY','CDBBC').'</button>
                                  </div>';

                                if(isset($all_coin_wall_add[$id.$tagvl]) && !empty($all_coin_wall_add[$id.$tagvl])){
                                    $classic_list .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';
                                }
                                $classic_list .='</li>';
                            }
                            else{                        
                                $classic_list .=  '<li class="cbd-classic-list">
                                <h2 class="CDBBC-title">'. $cdb_metamask_title . '</h2>';
                                $classic_list .= '<div class="tip-button" data-metamask-address="'.$metamask_wall_add.'"></div>';
                                if(isset($all_coin_wall_add[$id.$tagvl]) && !empty($all_coin_wall_add[$id.$tagvl])){
                                $classic_list .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';
                                }
                                $classic_list .= '<div class="message"></div></li>';                      
                            }
        
                        }
                        else {
                            $coin_tabs .= '<div class="dbd-tabs-content ' . $active_tab . '" id="'.$id.'-tab">';
  
                            if($id!='metamask'){                    
                                $coin_tabs.='<div class="CDBBC_qr_code"><img src="https://chart.googleapis.com/chart?cht=qr&chl='.$address.'&chs=260x260&chld=L|0" alt="Scan to Donate '. $coin_name .' to '. $address .'"/>';
                                 $coin_tabs .= '</div><div class="CDBBC_input_add">
                                <h2 class="CDBBC-title">'. $title_content . '</h2>
                                <p class="CDBBC-desc">' . $desc_content . '</p>';
                                if(isset($all_coin_wall_add[$id.$tagvl]) && !empty($all_coin_wall_add[$id.$tagvl])){
                                 $coin_tabs .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';
                                }
                                $coin_tabs .=  ' <input type="text" class="wallet-address-input"  id="' . $id . '-wallet-address" name="' . $id . '-wallet-address" value="' . $address . '">';

                                $coin_tabs .= '
                                <button class="CDBBC_btn" data-clipboard-target="#' . $id . '-wallet-address">
                                '.__('COPY','CDBBC').'</button></div>';                 
                            }
                            else{

                                 $coin_tabs.= '<div class="cdb-metamask-wrapper" >
                                <h2 class="CDBBC-title">'. $cdb_metamask_title . '</h2>
                                <p class="CDBBC-desc">' . $metamask_description . '</p>';
                                $coin_tabs .=  '<div class="tip-button" data-metamask-address="'.$metamask_wall_add.'"></div>';
                                if(isset($all_coin_wall_add[$id.$tagvl]) && !empty($all_coin_wall_add[$id.$tagvl])){
                                $coin_tabs .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'.$all_coin_wall_add[$id.$tagvl].'</div>';                   
                                }
                                $coin_tabs .='<div class="message"></div></div>';
                            }
                                $coin_tabs .='</div>';
                        }

                        $i++;
                    }    
        
                }

            

                if($design_type=='popup'){
                    wp_enqueue_style('cbd-jquery-modal', CDBBC_URL . 'assets/css/jquery.modal.min.css');
                    wp_enqueue_script('cbd-jquery-modal-js', CDBBC_URL . 'assets/js/jquery.modal.min.js',array('jquery'),true );
                     $output.= '<div class="cbd-list-container">
                    <div class="cbd-list-title"><h3>Donate</h3>
                    </div>
                    <div class="cbd-list-view">
                      <ul>';
                              $output .=  $list_view;
                              $output .=' 
                      </ul>
                    </div>
                    </div>';
        
                }
                elseif($design_type=='list'){
                    $output .='<div class="cbd-classic-container">';
                    $output .= '<ul class="cbd-classic-list">';
                    $output .= $classic_list;
                    $output .= '</ul></div>';
       
                }
                else{                
                    $output .='<div class="cbd-container">';
                    $output .= '<ul class="cbd-tabs" id="cbd-coin-list">' . $coin_links . '</ul>';
                    $output .= $coin_tabs;
                    $output .= '</div>';
                    
  
                }
            }
            else {
                $output.='<h6>'. __('Please Add coin wallet address in plugin settings panel', 'CDBBC').'</h6>';
            }   
        }
        else{
            if($metamask_wall_add!=''){
                $output.= '<div class="cdb-metamask-wrapper" >
                  <h2 class="CDBBC-title">'. $cdb_metamask_title . '</h2>
                    <p class="CDBBC-desc">' . $metamask_description . '</p>';
                   $output .=  '<div class="tip-button" data-metamask-address="'.$metamask_wall_add.'"></div>';
 
                if(isset($all_coin_wall_add['metamask_tag']) && !empty($all_coin_wall_add['metamask_tag'])){
                 $output .='<div class="cdbbc_tag"><span class="cdbbc_tag_heading">'.__("Tag/Note:-","CDBBC").' </span>'. $all_coin_wall_add['metamask_tag'].'</div>';
                }                                   
                $output.=  '<div class="message"></div></div>';      
            }
            else{
                $output.='<h6>'. __('Please Add ETH address for MetaMask in plugin settings panel', 'CDBBC').'</h6>';
            }
        }
        return $output;
    }
    /**
     * Code you want to run when all other plugins loaded.
     */
    public function cdbbc_load_lang()
    {
        load_plugin_textdomain('CDBBC', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');          
    }
    /**
     * Run when activate plugin.
     */
    public static function activate()
    {
       add_option('CDBBC_do_activation_redirect', true);       
       update_option("cdbbc_activation_time",date('Y-m-d h:i:s') ); 
       update_option("cdbbc-alreadyRated","no");         
    }
  
    function CDBBC_do_activation_redirect()
    {
        if (get_option('CDBBC_do_activation_redirect', false)) {
            delete_option('CDBBC_do_activation_redirect');
            if (!isset($_GET['activate-multi'])) {
                wp_redirect(admin_url('admin.php?page=cdb-settings'));
            }
        }
    }

    /**
     * Run when deactivate plugin.
     */
    public static function deactivate()
    {
    }

    function cdbbc_setting_panel_action_link($link){
		$link[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'admin.php?page=cdb-settings') ) .'">Settings</a>';
		return $link;
    }
}

function Cryptocurrency_Donation_Box()
{
    return Cryptocurrency_Donation_Box::get_instance();
}

$GLOBALS['Cryptocurrency_Donation_Box'] = Cryptocurrency_Donation_Box();