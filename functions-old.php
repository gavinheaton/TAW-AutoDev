<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* User roles for iHackOnline */

$result = add_role( 'hacker', __(

'Hacker' ),

array(

'read' => true, // true allows this capability
'edit_posts' => false, // Allows user to edit their own posts
'edit_pages' => false, // Allows user to edit pages
'edit_others_posts' => false, // Allows user to edit others posts not just their own
'create_posts' => false, // Allows user to create new posts
'manage_categories' => false, // Allows user to manage post categories
'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
)
);
$result = add_role( 'hackcoordinator', __(

'Hack Coordinator' ),

array(

'read' => true, // true allows this capability
'edit_posts' => false, // Allows user to edit their own posts
'edit_pages' => false, // Allows user to edit pages
'edit_workshops' => true, // Allows user to edit pages
'edit_others_posts' => false, // Allows user to edit others posts not just their own
'create_posts' => false, // Allows user to create new posts
'create_workshops' => true, // Allows user to create new posts
'manage_categories' => false, // Allows user to manage post categories
'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
)

);

function ihack_list_child_pages($atts) { 
 
  $atts = shortcode_atts(array(
    'page' => false,
    'remove' => '',
	'description' => "0",
//    'description' => true, //incude the description by setting this to true
    'words' => 60, // used if you need an excerpt
    'exclude' => false,
    'number' => false,
  ), $atts);
 
    global $post;
    $page = ($atts['page'] !== false) ? $atts['page'] : $post->ID;
 
    /* Excluded posts */
    $exclude = ($atts['exclude'] != false) ? $exclude = explode(',', $atts['exclude']) : array();
 
    $args = array(
        'post_parent' => $page,
        'post_type' => 'page',
        'post__not_in' => $exclude
    );
 
    /* Limit number of posts? (for pagination purposes) */
    $args['posts_per_page'] = ($atts['number'] !== false) ? $atts['number'] : '-1';
 
    $subpages = new WP_query($args);
     
    /* Build list of pages */
    if ($subpages->have_posts()) :
 
        $output = '<ul>';
 
        while ($subpages->have_posts()) : $subpages->the_post();
 
            if ( ($atts['remove'] != '') && ($atts['description']) ) $excerpt = trim(str_replace($atts['remove'], '', get_the_excerpt()));
            if ( ($atts['words'] !== false) && ($atts['description']) ) $excerpt = wp_trim_words($excerpt, $num_words = $atts['words'], $more = null );
            $output .= '<div style="width:150px; float:left; margin:20px"><strong><a class="teamName" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() .get_the_post_thumbnail().'</a></strong>';
            if ($atts['description']) $output .= '<p>' . $excerpt  . ' [<a href="' . get_permalink() . '" title="' . get_the_title() . '">more</a>]</p>';
            $output .= '</div>';
 
        endwhile;
        $output .= '</div>';
 
       /* Reset query */
       wp_reset_postdata();
 
    else :
 
        $output = '<p>No pages found.</p>';
 
    endif;
 
 return $output;
}
add_shortcode('ihack_childpages', 'ihack_list_child_pages');
