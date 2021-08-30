<?php
/* 	CHANGE HISTORY
	v1	User Roles for Hacker and Hack Coordinator:
		- Remove Admin bar when logged in
		- Add Logout button to Primary Menu
		iHack Child Pages function to display child pages under Event pages
		Show iHO Welcome Widget in Admin panel
		Remove comments from pages
	v2	Custom menus by role
	v3	Enable comments etc
	v4	Add menu function to show username, allow Hack Coordinator to edit and create RESOURCE and TEAMPAGES
	v5	Add smart fields to wp-forms and remove Logout button from v1
		
*/

/* Re-enable comments */
function default_comments_on( $data ) {
    if( $data['post_type'] == 'page' ) {
        $data['comment_status'] = 'open';
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'default_comments_on' );

/* Disable comments for greenrooms */
function greenroom_comments_off( $data ) {
    if( $data['post_type'] == 'greenroom' ) {
        $data['comment_status'] = 'closed';
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'greenroom_comments_off' );



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
'edit_resources' => true, // Allows user to edit Resources pages
'edit_teampages' => true, // Allows user to edit TeamPages
'edit_others_posts' => false, // Allows user to edit others posts not just their own
'create_posts' => false, // Allows user to create new posts
'create_resources' => true, // Allows user to create new Resources pages
'create_teampages' => true, // Allows user to create new Resources pages	
'manage_categories' => false, // Allows user to manage post categories
'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
'edit_themes' => false, // false denies this capability. User can’t edit your theme
'install_plugins' => false, // User cant add new plugins
'update_plugin' => false, // User can’t update any plugins
'update_core' => false // user cant perform core updates
)

);

/* v4 - add username to menu */
add_filter( 'wp_nav_menu_objects', 'my_custom_menu_item');
function my_custom_menu_item($items) {
    $remove_childs_of = array();
    foreach($items as $index => $item) {
        if($item->title == "#profile_name#") {
            if(is_user_logged_in()) {
                $user=wp_get_current_user();
                $name=$user->display_name; // or user_login , user_firstname, user_lastname
                $items[$index]->title = $name;
            }
            else {
                array_push($remove_childs_of, $item->ID);
                unset($items[$index]);
            }
        }
        if(!empty($remove_childs_of) && in_array($item->menu_item_parent, $remove_childs_of)) {
            array_push($remove_childs_of, $item->ID);
            unset($items[$index]);
        }
    }
    return $items;
}
/* v4 - end */

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
//        'post_type' => 'page',
		'post_type' => 'any',
        'post__not_in' => $exclude,
		'orderby' => 'post_title',
    	'order' => 'ASC',
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
            $output .= '<div class="teamContainer"><a class="teamName" href="' . get_permalink() .'">' . get_the_post_thumbnail($post_id,'thumbnail'). '<div class="teamTitle">' . get_the_title() .'</div></a>';
            if ($atts['description']) $output .= '<p>' . $excerpt  . ' [<a href="' . get_permalink() . '" title="' . get_the_title() . '">more</a>]</p>';
            $output .= '</div>';
 		// ($post_id,array(100,100))
        endwhile;
        $output .= '</div>';
 
       /* Reset query */
       wp_reset_postdata();
 
    else :
 
        $output = '<p>No team pages found.</p>';
 
    endif;
 
 return $output;
}
add_shortcode('ihack_childpages', 'ihack_list_child_pages');

function wps_parent_post(){
  global $post;
  if ($post->post_parent){
    $ancestors=get_post_ancestors($post->ID);
    $root=count($ancestors)-1;
    $parent = $ancestors[$root];
  } else {
    $parent = $post->ID;
  }
  if($post->ID != $parent){
	  $parent_icon = get_stylesheet_directory_uri() . "/images/Up-11-2.png";
	  echo '<style>
			.entry-title:before {
				content: url("' . $parent_icon . '");
				margin-right: 10px;
			}
		</style>';
      echo '<a href="'.get_permalink($parent).'" class="parent-post">Back to parent page</a>';
  }
}
add_shortcode('ihack_parent', 'wps_parent_post');

/* Disable Admin bar for all but Admins */
add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

/* Add logout button to menu 
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
function add_login_logout_link($items, $args) {
        ob_start();
        wp_loginout('index.php');
        $loginoutlink = ob_get_contents();
        ob_end_clean();
        $items .= '<li>'. $loginoutlink .'</li>';
    return $items;
}
*/

/* Admin widget for iHackOnline */
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
global $wp_meta_boxes;
wp_add_dashboard_widget('custom_widget', 'iHackOnline', 'custom_dashboard_information');
}
function custom_dashboard_information() {
	echo '<img width="300px" src="https://demo.ihackonline.com/wp-content/uploads/2020/03/Primary_iHack_RGB.png">';
	echo '<p>Welcome to iHackOnline by Disruptors Co - the easiest way to meet and collaborate online.</p><p>For support email gavin@disruptorsco.com.</p>';
}

/**
 * Using Smart Tags in Checkboxes for WP-forms.
 *
 * @link   https://wpforms.com/developers/process-smart-tags-in-checkbox-labels/
 *
 * @param  array $field
 * @param  array $deprecated
 * @param  array $form_data
 * @return array
 */
function wpf_dev_checkbox_choices_process_smarttags( $field, $deprecated, $form_data ) {
    foreach ( $field['choices'] as $key => $choice ) {
        if ( ! empty( $choice['label'] ) ) {
            $field['choices'][ $key ]['label'] = apply_filters( 'wpforms_process_smart_tags', $choice['label'], $form_data );
        }
    }
    return $field;
}
add_filter( 'wpforms_checkbox_field_display', 'wpf_dev_checkbox_choices_process_smarttags', 10, 3 );
	
