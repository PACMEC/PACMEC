<?php
/**
 * WordPress settings API demo class
 *

 */
if (!class_exists('CDB_Settings')) :
    class CDB_Settings
{

    private $settings_api;

    function __construct()
    {
        $this->settings_api = new CoolPlugins_Settings_API;

        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_init()
    {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }


    function admin_menu()
    {
        add_submenu_page( 'cool-crypto-plugins', 'Cryptocurrency Donation Box', 'Crypto Donation Box', 'manage_options', 'cdb-settings', array($this, 'plugin_page') );
    }

    function get_settings_sections()
    {

        
        $sections = array(
            array(
                'id' => 'wallet_addresses',
                'title' => __('Mostly used coin', 'coolplugins')
            ),
             array(
                'id' => 'wallet_addresses1',
                'title' => __('All other coins', 'coolplugins')
            ),
        
            array(
                'id' => 'coolplugins_advanced',
                'title' => __('Settings', 'coolplugins')
            ),
            array(
                'id' => 'coolplugins_shortcode',
                'title' => __('Shortcode', 'coolplugins')
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields()
    {
    
        $newid='';
        $coin_list= CDBBC_supported_coins();
        $coin_list1=array_slice($coin_list,0,13);
        $coin_list2=array_slice($coin_list,13);
        
       
        if(is_array($coin_list1)){
            foreach($coin_list1 as $id=>$name){
                $newid=$id.'_tag';
                if($name!='MetaMask'){
                    $coin_wallet_fields[]= array(
                        'name' =>$id,
                        'label' => __("$name Wallet Address", 'cdb'),
                        'desc' => __("Enter Your $name Wallet Address", 'cdb'),
                        'placeholder' => __('', 'cdb'),
                        'type' => 'text',
                        'default' => '',
                        //'sanitize_callback' => 'sanitize_text_field'
                    );
                    $coin_wallet_fields[]= array(
                        'name' =>$newid,
                        'placeholder' => __(" Tag/note/memo for your $name Wallet Address ", 'cdb'),
                        'type' => 'text',
                        'desc' => __('<hr><hr>','cdb'),
                        'default' => '',                      
                        //'sanitize_callback' => 'sanitize_text_field'
                    );             
                }
                 else{
                    $coin_wallet_fields[]= array(
                        'name' =>$id,
                        'label' => __(" Ethereum  Address For MetaMask", 'cdb'),
                        'desc' => __("Enter Your  Ethereum  Address For MetaMask", 'cdb'),
                        'placeholder' => __('', 'cdb'),
                        'type' => 'text',
                        'default' => '',
                        //'sanitize_callback' => 'sanitize_text_field'
                    );
                    $coin_wallet_fields[]= array(
                        'name' =>$newid,
                        'placeholder' => __(" Tag/note/memo for your $name Wallet Address ", 'cdb'),
                        'type' => 'text',
                        'desc' => __('<hr><hr>','cdb'),
                        'default' => '',                      
                        //'sanitize_callback' => 'sanitize_text_field'
                    );
                }
                
            }
        }

        //SECON LIST

  

            if(is_array($coin_list2)){
            foreach($coin_list2 as $id=>$name){
                $newid=$id.'_tag';
                if($name!='MetaMask'){
                    $coin_wallet_fields1[]= array(
                        'name' =>$id,
                        'label' => __("$name Wallet Address", 'cdb'),
                        'desc' => __("Enter Your $name Wallet Address", 'cdb'),
                        'placeholder' => __('', 'cdb'),
                        'type' => 'text',
                        'default' => '',
                        //'sanitize_callback' => 'sanitize_text_field'
                    );
                    $coin_wallet_fields1[]= array(
                        'name' =>$newid,
                        'placeholder' => __(" Tag/note/memo for your $name Wallet Address ", 'cdb'),
                        'type' => 'text',
                        'desc' => __('<hr><hr>','cdb'),
                        'default' => '',                      
                        //'sanitize_callback' => 'sanitize_text_field'
                    );             
                }
             
            }
        }
       
        $settings_fields = array(
            'wallet_addresses' =>array_filter($coin_wallet_fields),
            'wallet_addresses1' =>array_filter($coin_wallet_fields1),
           
            'coolplugins_advanced' => array(
                array(
                    'name' =>'maintitle-metamask',
                    'label' => __("MetaMask Title", 'cdb'),
                    'desc' => __("", 'cdb'),
                    'placeholder' => __('', 'cdb'),
                    'type' => 'text',
                    'default' => 'Donate With MetaMask',
                    //'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' => 'cdb-metamask-desc',
                    'label' => __("MetaMask Description", 'cdb'),
                    'desc' => __("", 'cdb'),
                    'placeholder' => __('', 'cdb'),
                    'type' => 'textarea',
                     'default' => 'Donate ETH Via PAY With Meta Mask',
                    //'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' =>'main-title',
                    'label' => __("Main Title", 'cdb'),
                    'desc' => __("", 'cdb'),
                    'placeholder' => __('', 'cdb'),
                    'type' => 'text',
                    'default' => 'Donate [coin-name] to this address',
                    //'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' => 'cdb-desc',
                    'label' => __("Description", 'cdb'),
                    'desc' => __("", 'cdb'),
                    'placeholder' => __('', 'cdb'),
                    'type' => 'textarea',
                    'default' => 'Scan the QR code or copy the address below into your wallet to send some [coin-name]',
                    //'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' => 'crypto_cool_plugins',
                    'label' => __('Plugin Review', 'cdb'),
                    'desc' => '<div style="background:#fffde7;padding:5px;border:1px solid #ddd;">
                            May I ask a review for our "<strong>Cryptocurrency Donation Box</strong>" - free WordPress plugin? This will help to spread its popularity and to make this plugin a better one.  <br><br><a href="https://wordpress.org/support/plugin/cryptocurrency-donation-box/reviews/#new-post" class="button button-primary" target="_blank">Submit Review ★★★★★</a></div>',
                     'type' => 'html'
                ),
            ),

            'coolplugins_shortcode' => array(
                array(
                    'name' => 'use_shortcode',
                    'label' => __('Add this shortcode anywhere(pages/posts)', 'coolplugins'),
                    'desc' => '<code style="font-size:24px;">[crypto-donation-box]</code>',
                     'type' => 'html'
                ),
                array(
                    'name' => 'use_tabular_shortcode',
                    'label' => __('Shortcode for Page/Posts', 'coolplugins'),
                    'desc' => '<code style="font-size:24px;">[crypto-donation-box type="tabular"]</code>',
                     'type' => 'html'
                ),
                array(
                    'name' => 'use_popup_shortcode',
                    'label' => __('Shortcode for sidebar/footers', 'coolplugins'),
                    'desc' => '<code style="font-size:24px;">[crypto-donation-box type="popup"]</code>',
                     'type' => 'html'
                ),
                array(
                 'name' => 'use_list_shortcode',
                'label' => __('Shortcode for Pages/sidebar/footer', 'coolplugins'),
                'desc' => '<code style="font-size:24px;">[crypto-donation-box type="list"]</code>',
                 'type' => 'html'
                ),
                array(
                    'name' => 'use_metamask_shortcode',
                   'label' => __('Shortcode for MetaMask', 'coolplugins'),
                   'desc' => '<code style="font-size:24px;">[crypto-donation-box type="metamask"]</code>',
                    'type' => 'html'
                ),           
               
            )           
        );
  
        return $settings_fields;
    }

    function plugin_page()
    {
        echo '<div class="wrap">

        <h1>';
        echo esc_html( get_admin_page_title() );
        echo '</h1>'; 
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages()
    {
        $pages = get_pages();
        $pages_options = array();
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;