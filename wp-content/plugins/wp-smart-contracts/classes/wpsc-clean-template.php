<?php
/**
 * Template Name: Clean Page
 * This template will only display the content you entered in the page editor
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php     
    $id = get_the_ID();
    if (get_post_meta($id, "wpsc_is_nft_minter", true)) {
        wp_enqueue_media();
        get_header();
    } elseif (
        get_post_meta($id, "wpsc_is_nft_my_items", true) or 
        get_post_meta($id, "wpsc_is_nft_my_bids", true) or 
        get_post_meta($id, "wpsc_is_nft_author", true)
    ) {
        WPSC_assets::loadNFTMy();
        get_header();
    } else {
        wp_head(); 
    }
    ?>
</head>
<body class="wpsc-popup">
<?php
    while ( have_posts() ) : the_post();  
        the_content();
    endwhile;
?>
<?php 
if (get_post_meta($id, "wpsc_is_nft_minter", true) or get_post_meta($id, "wpsc_is_nft_my_items", true) or get_post_meta($id, "wpsc_is_nft_author", true)) {
    get_footer();
} else {
    wp_footer();
}
?>
</body>
</html>
