<?php

if( ! defined( 'ABSPATH' ) ) die;

new WPSC_Queries();

/**
 * Handle etherscan api queries for block explorer view
 */

class WPSC_Queries {

    static public function nftAuthors() {

        global $wpdb;

        $authors = $wpdb->get_results("SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status=\"publish\" AND post_type=\"nft\"", ARRAY_A);
    
        if (!empty($authors)) {
            $authors = array_column($authors, 'post_author');
            return $wpdb->get_results("SELECT user_nicename FROM $wpdb->users WHERE ID in (".implode(",", $authors).")", ARRAY_A);
        }

        return [];

    }

    static public function nftCollections($selected=false, $get_deployed=false) {
        global $wpdb;
        $res = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status=\"publish\" AND post_type=\"nft-collection\" ORDER BY post_title", ARRAY_A);
        if (is_array($res) and ($selected or $get_deployed)) {
            foreach($res as $i => $row) {
                if ($row["ID"]==$selected) {
                    $res[$i]["selected"] = true;
                    if (!$get_deployed) {
                        return $res;
                    }
                }
                if (empty($res[$i]["post_title"])) {
                    $res[$i]["post_title"] = "Untitled";
                }
                if ($get_deployed and $contract = get_post_meta($row["ID"], 'wpsc_contract_address', true)) {
                    $res[$i]["deployed"] = $contract;
                }
                if ($get_deployed and $net = get_post_meta($row["ID"], 'wpsc_network', true)) {
                    $res[$i]["network"] = $net;
                }
            }
        }
        return $res;
    }

    static public function getTaxonomy($tax) {
        global $wpdb;
        return $wpdb->get_results("SELECT t.term_id, t.name FROM $wpdb->term_taxonomy tax, $wpdb->terms t  WHERE tax.term_id=t.term_id AND taxonomy=\"".$tax."\" ORDER BY t.name", ARRAY_A);
    }

    static public function getNFTsByID($ids, $contract) {

        $str = str_replace(',', '', $ids);
        if (!ctype_digit($str)) return;

        if (!preg_match('/^(0x)?[0-9a-fA-F]{40}$/', $contract, $output_array)) return;

        global $wpdb;
        $res = $wpdb->get_results("
        SELECT p.ID
        FROM $wpdb->posts p, $wpdb->postmeta m, $wpdb->postmeta m2
        WHERE 
            p.post_type=\"nft\" AND 
            p.post_status=\"publish\" AND 
            p.ID=m.post_id AND 
            p.ID=m2.post_id AND 
            p.post_status=\"publish\" AND
            m.meta_key=\"wpsc_nft_id\" AND 
            m.meta_value IN ($ids) AND 
            m2.meta_key=\"wpsc_collection_contract\" AND 
            m2.meta_value=\"$contract\"", ARRAY_A);
        return $res;
    }

    static public function getNFTIdsByAuthor($collection_id, $author_id) {

        $collection_id = (int) $collection_id;
        if ($collection_id==0) return [];

        $author_id = (int) $author_id;
        if ($author_id==0) return [];

        global $wpdb;
        return $wpdb->get_results("
        SELECT m2.meta_value as nft_id 
        FROM $wpdb->postmeta m, 
        $wpdb->postmeta m2,
        $wpdb->posts p
        WHERE m.post_id=m2.post_id AND 
        m.meta_value=$collection_id AND 
        p.ID=m.post_id AND
        m.meta_key=\"wpsc_item_collection\" AND 
        m2.meta_key=\"wpsc_nft_id\" AND
        p.post_author=$author_id AND
        p.post_status=\"publish\"", ARRAY_A);
        
    }

    static public function getNFTIdsByTaxonomy($collection_id, $tag_id) {

        $collection_id = (int) $collection_id;
        if ($collection_id==0) return [];

        $tag_id = (int) $tag_id;
        if ($tag_id==0) return [];

        global $wpdb;
        return $wpdb->get_results("
        SELECT m2.meta_value as nft_id 
        FROM $wpdb->postmeta m, 
        $wpdb->postmeta m2,
        $wpdb->term_relationships t,
        $wpdb->posts p
        WHERE m.post_id=m2.post_id AND 
        p.ID=m.post_id AND
        m.meta_value=$collection_id AND 
        t.object_id=m.post_id AND
        m.meta_key=\"wpsc_item_collection\" AND 
        m2.meta_key=\"wpsc_nft_id\" AND
        t.term_taxonomy_id=$tag_id AND
        p.post_status=\"publish\"", ARRAY_A);

    }

    static public function getNFTIds($collection_id) {

        $collection_id = (int) $collection_id;

        if ($collection_id==0) return [];

        global $wpdb;
        return $wpdb->get_results("
        SELECT m2.meta_value as nft_id 
        FROM $wpdb->postmeta m, 
        $wpdb->postmeta m2,
        $wpdb->posts p
        WHERE m.post_id=m2.post_id AND 
        p.ID=m.post_id AND
        m.meta_value=$collection_id AND 
        m.meta_key=\"wpsc_item_collection\" AND 
        m2.meta_key=\"wpsc_nft_id\" AND
        p.post_status=\"publish\"", ARRAY_A);
    }

    static public function clearNFTTokenURI() {
        global $wpdb;
        $wpdb->query("DELETE FROM `wp_options` WHERE option_name LIKE '%_transient_wpsc_nft_%'");
    }

}
