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

<div class="buddypress-wrap">

	<?php if ( is_user_logged_in() && ( is_space_admin() ) ) : ?>

		<div class="bp-business-profile-post-form-wrapper">

			<?php bp_get_template_part( 'activity/post-form' ); ?>

		</div>
	<?php endif; ?>

	<div class="screen-content">

		<?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

		<div id="activity-stream" class="activity" data-bp-list="activity" >

			<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>

		</div><!-- .activity -->

		<?php bp_nouveau_after_activity_directory_content(); ?>
	</div><!-- // .screen-content -->
</div>
