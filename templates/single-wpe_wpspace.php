<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp;

if ( false === strpos( $wp->request, 'medias' ) ) {
	buddypress()->current_component = 'activity';
}


get_header(); /*Header Portion*/

/**
 * Hook: bp_business_profile_before_main_content. *
 */
do_action( 'bp_business_profile_before_main_content' );
?>

<div id="primary" class="content-area">

	<main id="main" class="site-main buddypress-wrap" role="main">
	
		<?php do_action( 'before_bp_single_business' ); ?>
		
		<div id="buddypress" class="bp-business-single <?php echo esc_attr( bp_nouveau_get_container_classes() ); ?>" >
			
			<?php
			while ( have_posts() ) :
				the_post();

				the_content();
			endwhile;
			?>
			
		</div>
		
		<?php do_action( 'after_bp_single_business' ); ?>
		
	</main> <!-- Main tag finish -->
	
</div>

<?php
/**
 * Hook: bp_business_profile_after_main_content.
 */
do_action( 'bp_business_profile_after_main_content' );


get_footer();
