<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category               = get_the_terms( get_the_ID(), 'wp_space_category' );
$category_name          = '';
if ( ! empty( $category ) ) {
	$category_name = $category['0']->name;
}

?>

<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="space-list-wrap">
		<a href="<?php the_permalink(); ?>">
			<div class="space-cover-img">

					<?php default_cover_image(); ?>

				<?php if ( $category_name != '' ) : ?>

					<span class="space-category"><?php echo esc_html( $category_name ); ?></span>

				<?php endif; ?>

			</div>
		</a>
		<div class="space-content-wrap">
			<h3>
				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html( the_title() ); ?></a>
			</h3>
		</div>
	</div>
</li><!-- #post-## -->
