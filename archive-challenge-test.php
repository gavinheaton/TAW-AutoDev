<?php

/* ===== 

Template Name: Custom Post Type Archive

===== */

get_header();


$custom_post_type_function = new WP_Query(
	
	array(
		'post_type' => 'challenge',
		'post_status' => 'publish',
		'posts_per_page' => 8,
		'order' => 'DESC',
		'paged' => $paged
	));

$classIncrement = 0;

?> 

<div id="main-content">

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
		<div class="entry-content">

			<div class="et_pb_section et_section_regular">

				<div class="et_pb_row et_pb_gutters2 et_pb_row_fullwidth et_pb_row_4col">
					
					<?php
                	if($custom_post_type_function ->have_posts()) {
					$i = 0;
                    while($custom_post_type_function ->have_posts()): $custom_post_type_function ->the_post();?>
				
					<div class="et_pb_column et_pb_column_<?php echo $classIncrement++ ?> et_pb_column_1_4">

						<div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left">
				
							<div class="et_pb_text_inner">
								<h2><a class="teamName" href="<?php get_permalink(); ?>"><?php the_title();?></a></h2>
							</div>
			
						</div> <!-- .et_pb_text -->
						
					</div> <!-- .et_pb_column -->
                    
                    <?php
                    $i++;
					if($i % 4 == 0) echo '</div><div class="et_pb_row et_pb_gutters2 et_pb_row_fullwidth et_pb_row_4col" >';
					endwhile;
					} 
					?>

			    </div> <!-- row -->

			</div> <!-- section -->
	         					<?php get_sidebar(); ?>
		</div> <!-- .entry-content -->

	</article> <!-- .et_pb_post -->

</div>

<?php get_footer(); ?>