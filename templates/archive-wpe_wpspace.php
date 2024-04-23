<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); /*Header Portion*/

/**
 * Hook: bp_business_profile_before_main_content. *
 */
do_action( 'bp_business_profile_before_main_content' );

global $bP_business_settings;
$orderby = ( isset( $bP_business_settings['general_settings']['orderby'] ) ) ? $bP_business_settings['general_settings']['orderby'] : 'date';
$order   = ( $orderby === 'title' ) ? 'ASC' : 'DESC';
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main buddypress-wrap" role="main">
		
		<header class="bp-business-profile-header">
			
			<h1 class="bp-business-profile-header__title page-title"><?php echo post_type_archive_title( '', false ); ?></h1>
			
		</header>
	
		<div id="bp-businesses-content" class="entry-content bp-businesses-content">
			<nav class="bp-business-profile-type-navs main-navs bp-navs dir-navs bp-subnavs business-main-nav" role="navigation" aria-label="Directory menu">
				<ul class="component-navigation business-nav">
				
					<li id="bp-business-profile-bp-index-all" class="bp-business-profile-index-scope-link selected" data-bp-scope="all">
						<a data-scope="all" href="">
							<?php esc_html_e( 'All Businesses', 'bp-business-profile' ); ?>
						</a>
					</li>

					<li id="bp-business-profile-bp-index-personal" class="bp-business-profile-index-scope-link" data-bp-scope="personal">
						<a data-scope="personal" href=""><?php esc_html_e( 'My Businesses', 'bp-business-profile' ); ?></a>
					</li>
					
					<?php if ( bp_business_profile_can_create_business( wp_get_current_user()->ID ) ) : ?>
						<li id="bp-business-profile-create-business-link" class="no-ajax business-create create-button">						
							<a href="<?php echo esc_url( get_post_type_archive_link( 'business' ) ); ?>create-business-page/">
								<?php printf( __( 'Create a %s', 'bp-business-profile' ), bp_business_profile_get_singular_label() ); /* translators: %s: The singular label for a Business */ ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</nav>
			
			
			<div class="subnav-filters filters no-ajax" id="subnav-filters">

				<div class="subnav-search clearfix">
						
					<div class="dir-search business-search bp-search" data-bp-search="business">
						<form action="" method="get" class="bp-dir-search-form" id="dir-business-search-form" role="search">

							<label for="dir-business-search" class="bp-screen-reader-text"><?php esc_html_e( 'Search business...', 'bp-business-profile' ); ?></label>

							<input id="dir-business-search" name="business_search" type="search" placeholder="<?php esc_html_e( 'Search business...', 'bp-business-profile' ); ?>">

							<button type="submit" id="dir-business-search-submit" class="nouveau-search-submit" name="dir_business_search_submit">
								<span class="dashicons dashicons-search" aria-hidden="true"></span>
								<span id="button-text" class="bp-screen-reader-text"><?php esc_html_e( 'Search', 'bp-business-profile' ); ?></span>
							</button>

						</form>
					</div>

				</div>
				
				<div id="comp-filters" class="component-filters clearfix">
					<div id="business-order-select" class="last filter">
						<label for="business-order-by" class="bp-screen-reader-text">
							<span><?php esc_html_e( 'Order By:', 'bp-business-profile' ); ?></span>
						</label>
						<div class="select-wrap">
							<select id="business-order-by" data-bp-filter="business">								
								<option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Newly Created', 'bp-business-profile' ); ?></option>
								<option value="title" <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Alphabetical', 'bp-business-profile' ); ?></option>
							</select>
							<span class="select-arrow" aria-hidden="true"></span>
						</div>
					</div>
				</div>
				
			</div>
			
			<div id="business-dir-list" class="business dir-list" data-bp-list="business">
				<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-business-loading' ); ?></div>
			</div><!-- #business-dir-list -->
		</div> <!-- Entry Content finish -->
		
		
	</main> <!-- Main tag finish -->
</div>
<?php
/**
 * Hook: bp_business_profile_after_main_content.
 */
do_action( 'bp_business_profile_after_main_content' );


get_footer();
