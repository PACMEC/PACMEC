<?php
/**
 * Displays footer site info
 *
 * @package PACMEC
 * @subpackage PACMEC_TwentySeventeen
 * @since 1.0.0
 * @version 1.0.0
 */

?>
<div class="site-info">
	<?php
	if ( function_exists( 'the_privacy_policy_link' ) ) {
		the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
	}
	?>
	<a href="<?php echo esc_url( __( 'https://www.pacmec.com.co/', 'pacmec-twentyseventeen' ) ); ?>" class="imprint">
		<?php printf( __( 'Proudly powered by %s', 'pacmec-twentyseventeen' ), 'PACMEC' ); ?>
	</a>
</div><!-- .site-info -->
