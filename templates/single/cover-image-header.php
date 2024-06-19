<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cover_image = default_cover_image( true );

?>

<div id="cover-image-container">
	<div id="header-cover-image" style="background-image:url(<?php echo esc_url( $cover_image ); ?>);"></div>

	<div class="container">
		<div id="item-header-cover-image">
			<div id="item-header-content">

				<div class="item-title">
					<h2 class="user-nicename"><?php the_title(); ?></h2>
				</div>

			</div>

		</div><!-- #item-header-cover-image -->
	</div><!-- .container -->

</div><!-- #cover-image-container -->
