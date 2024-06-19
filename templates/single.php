<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp;

get_header(); /*Header Portion*/

do_action( 'space_before_main_content' );
?>

<div id="primary" class="content-area">

	<main id="main" class="site-main buddypress-wrap" role="main">

		<?php do_action( 'before_single_space' ); ?>

		<div id="buddypress" class="space-single <?php echo esc_attr( bp_nouveau_get_container_classes() ); ?>" >

			<?php
			while ( have_posts() ) :
				the_post();

				include WPE_WPS_PLUGIN_DIR . '/templates/single/single-content.php';
			endwhile;
			?>

		</div>

		<?php do_action( 'after_single_space' ); ?>

	</main> <!-- Main tag finish -->

</div>

<?php
/**
 * Hook: bp_business_profile_after_main_content.
 */
do_action( 'space_after_main_content' );


get_footer();
