<?php
/*
 * Template Name: Workshop Form Template
 * description: >-
  Page template for iHack Workshop forms
 * underneath the page content
 */

get_header(); ?>

<div id="primary" class="site-content">
  <div id="content" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

      <header class="entry-header">
        <?php the_post_thumbnail(); ?>
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>

      <div class="entry-content">
        <?php the_content(); ?>
        <?php
          $args = array(
            'post_type' => 'workshop', // enter custom post type
            'orderby' => 'date',
            'order' => 'DESC',
          );

          $loop = new WP_Query( $args );
          if( $loop->have_posts() ):
          while( $loop->have_posts() ): $loop->the_post(); global $post;
            echo '<div class="portfolio">';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<div class="portfolio-image">'. get_the_post_thumbnail( $id ).'</div>';
            echo '<div class="portfolio-work">'. get_the_content().'</div>';
            echo '</div>';
          endwhile;
          endwhile;
          endif;
        ?>
      </div><!-- #entry-content -->
      <?php comments_template( ’, true ); ?>
    <?php endwhile; // end of the loop. ?> 
  </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
