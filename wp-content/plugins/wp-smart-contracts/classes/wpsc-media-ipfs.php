<?php

if( ! defined( 'ABSPATH' ) ) die;

new WPSC_IPFS_MEDIA();

class WPSC_IPFS_MEDIA {

    function __construct() {
        add_filter('manage_media_columns', [$this, 'setIPFSColumn']);
        add_action('manage_media_custom_column', [$this, 'setIPFSData']);
        add_filter("attachment_fields_to_edit", [$this, 'setIPFSLinks'], null, 2);
    }

    public function setIPFSColumn( $columns ) {
        $columns['wpsc-ipfs'] = 'IPFS';
        return $columns;
    }

    public function setIPFSData( $column ) {
        global $post;
        if ($column == "wpsc-ipfs") {
            echo self::showIPFS($post->ID, $post->wpsc_nft_ipfs);
        }
    }

    public function setIPFSDetail($form_fields, $post) {
        if( substr($post->post_mime_type, 0, 5) == 'image') {
            $user = new WP_User( $post->post_author );
            $form_fields["IPFS"]["input"] = "html"; 
            $form_fields["IPFS"]["html"] = self::showIPFS($post->ID, $post->wpsc_nft_ipfs);
        }
        return $form_fields;  
    }

    public function setIPFSLinks($form_fields, $post) {
        $form_fields["ipfs"]["label"] = "IPFS"; 
        $form_fields["ipfs"]["input"] = "html"; 
        $form_fields["ipfs"]["html"] = self::showIPFS($post->ID, $post->wpsc_nft_ipfs);
        return $form_fields;  
    }

    static private function showIPFS($id, $url) {
        $m = new Mustache_Engine;
        if ($url) {
            $ipfs_id = substr(str_replace("https://ipfs.io/ipfs/", "", $url), 0, 24) . "...";
            return $m->render(WPSC_Mustache::getTemplate('ipfs-link'), [
                "url"=>$url,
                "ipfs_id"=>$ipfs_id,
                "img"=>plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ipfs-blue.png'
            ]);
        } else {
            return $m->render(WPSC_Mustache::getTemplate('ipfs-button'), [
                "id"=>$id,
                "img"=>plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ipfs.png'
            ]);
        }
    }

    static public function getIpfsFromArr($arr) {
        if (is_array($arr)) {
            $res = [];
            foreach ($arr as $a) {
                if ($a["id"]) {
                    if ($ipfs = get_post_meta($a["id"], "wpsc_nft_ipfs", true)) {
                        $a["url"]=$ipfs;
                    }
                    $res[]=$a;
                }
            }
            return $res;
        }
	}

    static public function getMimeFromArr($arr) {
        if (is_array($arr) and array_key_exists(0, $arr) and array_key_exists("mime", $arr[0])) {
            if (strpos($arr[0]["mime"], "video")!==false) return "video";
            if (strpos($arr[0]["mime"], "image")!==false) return "image";
            if (strpos($arr[0]["mime"], "audio")!==false) return "audio";
            if (strpos($arr[0]["mime"], "application")!==false) return "doc";
        }
	}
        
}
