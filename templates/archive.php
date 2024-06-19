<?php

namespace SpacesEngine;

get_header(); ?>

<?php do_action( 'wpe_wps_before_spaces_index' ); ?>

	<div id="primary" class="content-area">
		<div id="content" class="buddypress-wrap" role="main">

		<div id="space-archive-container" data-nonce="<?php echo esc_attr( wp_create_nonce( 'spaces-index' ) ); ?>" data-pagination="standard">
			<?php do_action( 'space_archive_start' ); ?>
				<header class="entry-header">
					<h1 class="entry-title"><?php get_plural_label(); ?></h1>
				</header>

				<nav class="spaces-type-navs main-navs bp-navs dir-navs  bp-subnavs" role="navigation" aria-label="Directory menu">
					<ul class="component-navigation spaces-nav" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpe-wps-index-scope' ) ); ?>">
						<li id="wpe-wps-index-all" class="wpe-wps-index-scope-link selected">
							<a data-scope="all" href="">
								<div class="bb-component-nav-item-point">
									<?php
									printf(
									/* translators: %s: The plural label for a Space */
										esc_html__( 'All %s', 'wpe-wps' ),
										esc_html( get_plural_label() ),
									);
									?>
								</div>
							</a>
						</li>

						<?php if ( is_user_logged_in() ) : ?>
							<li id="wpe-wps-index-personal" class="wpe-wps-index-scope-link">
								<a data-scope="personal" href="">
									<div class="bb-component-nav-item-point">
										<?php
										printf(
										/* translators: %s: The plural label for a Space */
											esc_html__( 'My %s', 'wpe-wps' ),
											esc_html( get_plural_label() ),
										);
										?>
									</div>
								</a>
							</li>

							<li id="wpe-wps-create-space-archive-link" class="no-ajax space-create create-button">
								<a href="<?php echo esc_url( get_post_type_archive_link( 'wpe_wpspace' ) . 'create-' . strtolower( get_singular_label() ) . '-page/' ); ?>">
									<?php
									printf(
									/* translators: %s: The singular label for a Space */
										esc_html__( 'Create a %s', 'wpe-wps' ),
										esc_html( get_singular_label() ),
									);
									?>
								</a>
							</li>
						<?php endif; ?>
					</ul><!-- .component-navigation -->
				</nav>

			<div class="bp-secondary-header">
				<div class="form-group">
					<label for="wpe-wps-index-ordering"></label>
					<select id="wpe-wps-index-ordering" name="wpe_wps_order_filter" data-placeholder="<?php esc_attr_e( 'Order by', 'wpe-wps' ); ?>">
						<option value="latest"><?php esc_html_e( 'Latest', 'wpe-wps' ); ?></option>
						<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'wpe-wps' ); ?></option>
					</select>
				</div>
			</div>

			<div id="wpe-wps-index-main-wrapper">

					<div id="wpe-wps-index-sidebar">

						<?php require WPE_WPS_PLUGIN_DIR . '/templates/parts/space-filters.php'; ?>

					</div>

				<div id="wpe-wps-index-content-wrapper">

					<div class="status"></div>
					<div class="space-archive-wrapper"></div>

				</div>

			</div>
		</div>
		<?php do_action( 'space_archive_end' ); ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php do_action( 'wpe_wps_after_spaces_index' ); ?>

<?php get_footer(); ?>
