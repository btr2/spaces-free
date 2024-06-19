<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php esc_url( get_permalink( $post ) ); ?>"><?php esc_html( the_title() ); ?></a>
</article><!-- #post-## -->
