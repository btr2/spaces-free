<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="item-header" role="complementary" data-bp-item-id="<?php the_ID(); ?>" data-bp-item-component="space" class="space-header single-headers">

	<?php do_action( 'spaces_engine_before_profile_header_part' ); ?>

	<?php space_header_template_part(); ?>

	<?php do_action( 'spaces_engine_after_profile_header_part' ); ?>

</div><!-- #item-header -->

<div class="container">
	<div class="bp-wrap">

		<div id="item-body" class="item-body">

		</div><!-- #item-body -->

	</div><!-- // .bp-wrap -->
</div><!-- // .container -->
