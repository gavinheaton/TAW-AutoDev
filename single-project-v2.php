<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

$show_navigation = get_post_meta( get_the_ID(), '_et_pb_project_nav', true );

?>

<div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<div class="et_main_title">
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<span class="et_project_categories"><?php echo get_the_term_list( get_the_ID(), 'project_category', '', ', ' ); ?></span>
						<?php if ( ! $is_page_builder_used || ( $is_page_builder_used && 'on' === $show_navigation ) ) : ?>
						<div class="nav-single clearfix">
						<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . et_get_safe_localization( _x( '&larr;', 'Previous post link', 'Divi' ) ) . '</span> %title' ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . et_get_safe_localization( _x( '&rarr;', 'Next post link', 'Divi' ) ) . '</span>' ); ?></span>
						</div><!-- .nav-single -->

				<?php endif; ?>

					</div>

				<?php endif; ?>

					<div class="entry-content">
					<?php
						the_content();

						if ( ! $is_page_builder_used )
							wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
					</div> <!-- .entry-content -->

				<?php if ( ! $is_page_builder_used ) : ?>

					<?php
						if ( ! in_array( $page_layout, array( 'et_full_width_page', 'et_no_sidebar' ) ) ) {
							//turn off the metabox for the project
							//et_pb_portfolio_meta_box();
						}
					?>

				<?php endif; ?>

				<?php if ( ! $is_page_builder_used || ( $is_page_builder_used && 'on' === $show_navigation ) ) : ?>

					<div class="nav-single clearfix">
						<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . et_get_safe_localization( _x( '&larr;', 'Previous post link', 'Divi' ) ) . '</span> %title' ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . et_get_safe_localization( _x( '&rarr;', 'Next post link', 'Divi' ) ) . '</span>' ); ?></span>
					</div><!-- .nav-single -->

				<?php endif; ?>

				</article> <!-- .et_pb_post -->

			<?php
				if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_postcomments', 'on' ) )
					comments_template( '', true );
			?>
			<?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

			</div> <!-- #left-area -->

			<?php
				if ( in_array( $page_layout, array( 'et_full_width_page', 'et_no_sidebar' ) ) ) {
					et_pb_portfolio_meta_box();
				}
			?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->

<?php

get_footer();
