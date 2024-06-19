<?php

namespace SpacesEngine;

get_header(); ?>

	<div class="buddypress-wrap">
		<div id="create-space-form-wrapper">

			<?php if ( is_user_logged_in() ) : ?>

			<div class="item-body" id="create-space-body">

				<form action="" method="post" id="create-space-form" class="standard-form" enctype="multipart/form-data" data-nonce="<?php echo esc_attr( wp_create_nonce( 'create_space' ) ); ?>">
						<fieldset>
							<label for="space-name"><?php esc_html_e( 'Title', 'wpe-wps' ); ?></label>
							<input type="text" name="space_name" id="space-name">
						</fieldset>

						<fieldset>
							<input id="space-submit" type="submit" name="space_submit" value="<?php esc_attr_e( 'Submit', 'wpe-wps' ); ?>">
						</fieldset>
				</form><!-- #create-space-form -->

				<div id="create-space-result" style="display: none">
					<span id="create-space-result-success">
					<?php
					printf(
						// translators: Placeholder %s is the singular label of the space post type.
						esc_html__( '%1$s created successfully. To visit your new %2$s, click the button.', 'wpe-wps' ),
						esc_html( get_singular_label() ),
						esc_html( get_singular_label() )
					);
					?>
						</span>

				<a id="space-visit"><button class="primary">
				<?php
				printf(
						// translators: Placeholder %s is the singular label of the space post type.
					esc_html__( 'Visit %s', 'wpe-wps' ),
					esc_html( get_singular_label() )
				);
				?>
						</button></a>
				</div>

		</div><!-- .item-body -->
			<?php else : ?>
				<div class="logged-out-message">
					<?php
					printf(
					// translators: Placeholder %s is the singular label of the space post type.
						esc_html__( 'Please log in to create a %s', 'wpe-wps' ),
						esc_html( get_singular_label() )
					);
					?>
				</div>
			<?php endif; ?>
		</div>

	</div>

<?php

get_footer();
