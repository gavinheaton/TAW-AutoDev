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
	v6  Horizontal display of childpages
	v7  Front end form
	v8  Announcements ticker
	v9  Custom fields re-enabled with ACF plugin
	v10 Change the shape of the team page icons
	v11 Add Custom Fields for TAW Communicator
	v12 Add dashboard widget

*/

/* v12 - Dashboard widget */
// Register the function
function TAW_dashboard_widget() {
	wp_add_dashboard_widget('TAW_widget','TheAir.Works', 'TAW_widget_display');
}
// Add the action
add_action( 'wp_dashboard_setup', 'TAW_dashboard_widget' );

function TAW_widget_display() {
	$themeDir = get_site_url();
    echo '<img width="300px" src="'.$themeDir.'/wp-content/themes/divi-child/images/TAW-2021.png"><p>Welcome to TheAir.Works.<p>
		<p>Version: 1.12</p>';
}

/* v11 - Advanced Custom Fields for TAW Communicator */
if( function_exists('acf_add_options_page') ) {
		acf_add_options_page(array(
		'page_title' 	=> 'TheAir.Works Hackathon Details',
		'menu_title'	=> 'Hack Details',
		'menu_slug' 	=> 'taw-hack',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}


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
	$post_id = get_the_ID();
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
				'post_type' => 'any',
        'post__not_in' => $exclude,
				'orderby' => 'post_title',
				'order' => 'ASC',
    );

    /* Limit number of posts? (for pagination purposes) */
    $args['posts_per_page'] = ($atts['number'] !== false) ? $atts['number'] : '-1';

    $subpages = new WP_query($args);
    add_image_size( 'custom-size', 150, 75 );

    /* Build list of pages */
    if ($subpages->have_posts()) :

        $output = '<ul>';

        while ($subpages->have_posts()) : $subpages->the_post();

            if ( ($atts['remove'] != '') && ($atts['description']) ) $excerpt = trim(str_replace($atts['remove'], '', get_the_excerpt()));
            if ( ($atts['words'] !== false) && ($atts['description']) ) $excerpt = wp_trim_words($excerpt, $num_words = $atts['words'], $more = null );
            $output .= '<div class="teamContainer"><a class="teamName" href="' . get_permalink() .'">' . get_the_post_thumbnail($post_id,'custom-size',array(320,180)). '<div class="teamTitle">' . get_the_title() .'</div></a>';
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

/* v6 horizontal child page grid */
function ihack_horizontal($atts) {

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
//      'post_type' => 'page',
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

        $output = '<div class="ihackHorizontal">';

        while ($subpages->have_posts()) : $subpages->the_post();

            if ( ($atts['remove'] != '') && ($atts['description']) ) $excerpt = trim(str_replace($atts['remove'], '', get_the_excerpt()));
            if ( ($atts['words'] !== false) && ($atts['description']) ) $excerpt = wp_trim_words($excerpt, $num_words = $atts['words'], $more = null );
            $output .= '<div id="displayContainer"><a href="' . get_permalink() .'">' . get_the_post_thumbnail($post_id,'thumbnail'). '</a><p class="teamTitle">' . get_the_title() .'</p>';
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
add_shortcode('ihack_horizontal', 'ihack_horizontal');

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

function wpf_entries_table( $atts ) {

    // Pull ID shortcode attributes.
    $atts = shortcode_atts(
        [
            'id'     => '',
            'user'   => '',
            'fields' => '',
            'number' => '',
        ],
        $atts
    );

    // Check for an ID attribute (required) and that WPForms is in fact
    // installed and activated.
    if ( empty( $atts['id'] ) || ! function_exists( 'wpforms' ) ) {
        return;
    }

    // Get the form, from the ID provided in the shortcode.
    $form = wpforms()->form->get( absint( $atts['id'] ) );

    // If the form doesn't exists, abort.
    if ( empty( $form ) ) {
        return;
    }

    // Pull and format the form data out of the form object.
    $form_data = ! empty( $form->post_content ) ? wpforms_decode( $form->post_content ) : '';

    // Check to see if we are showing all allowed fields, or only specific ones.
    $form_field_ids = ! empty( $atts['fields'] ) ? explode( ',', str_replace( ' ', '', $atts['fields'] ) ) : [];

    // Setup the form fields.
    if ( empty( $form_field_ids ) ) {
        $form_fields = $form_data['fields'];
    } else {
        $form_fields = [];
        foreach ( $form_field_ids as $field_id ) {
            if ( isset( $form_data['fields'][ $field_id ] ) ) {
                $form_fields[ $field_id ] = $form_data['fields'][ $field_id ];
            }
        }
    }

    if ( empty( $form_fields ) ) {
        return;
    }

    // Here we define what the types of form fields we do NOT want to include,
    // instead they should be ignored entirely.
    $form_fields_disallow = apply_filters( 'wpforms_frontend_entries_table_disallow', [ 'divider', 'html', 'pagebreak', 'captcha' ] );

    // Loop through all form fields and remove any field types not allowed.
    foreach ( $form_fields as $field_id => $form_field ) {
        if ( in_array( $form_field['type'], $form_fields_disallow, true ) ) {
            unset( $form_fields[ $field_id ] );
        }
    }

    $entries_args = [
        'form_id' => absint( $atts['id'] ),
    ];

    // Narrow entries by user if user_id shortcode attribute was used.
    if ( ! empty( $atts['user'] ) ) {
        if ( $atts['user'] === 'current' && is_user_logged_in() ) {
            $entries_args['user_id'] = get_current_user_id();
        } else {
            $entries_args['user_id'] = absint( $atts['user'] );
        }
    }

    // Number of entries to show. If empty, defaults to 30.
    if ( ! empty( $atts['number'] ) ) {
        $entries_args['number'] = absint( $atts['number'] );
    }

    // Get all entries for the form, according to arguments defined.
    // There are many options available to query entries. To see more, check out
    // the get_entries() function inside class-entry.php (https://a.cl.ly/bLuGnkGx).
    $entries = wpforms()->entry->get_entries( $entries_args );

    if ( empty( $entries ) ) {
        return '<p>No entries found.</p>';
    }

    ob_start();

    echo '<table class="wpforms-frontend-entries">';

        echo '<thead><tr>';

            // Loop through the form data so we can output form field names in
            // the table header.
            foreach ( $form_fields as $form_field ) {

                // Output the form field name/label.
                echo '<th>';
                    echo esc_html( sanitize_text_field( $form_field['label'] ) );
                echo '</th>';
            }

        echo '</tr></thead>';

        echo '<tbody>';

            // Now, loop through all the form entries.
            foreach ( $entries as $entry ) {

                echo '<tr>';

                // Entry field values are in JSON, so we need to decode.
                $entry_fields = json_decode( $entry->fields, true );

                foreach ( $form_fields as $form_field ) {

                    echo '<td>';

                        foreach ( $entry_fields as $entry_field ) {
                            if ( absint( $entry_field['id'] ) === absint( $form_field['id'] ) ) {
                                echo apply_filters( 'wpforms_html_field_value', wp_strip_all_tags( $entry_field['value'] ), $entry_field, $form_data, 'entry-frontend-table' );
                                break;
                            }
                        }

                    echo '</td>';
                }

                echo '</tr>';
            }

        echo '</tbody>';

    echo '</table>';

    $output = ob_get_clean();

    return $output;
}
add_shortcode( 'wpforms_entries_table', 'wpf_entries_table' );

// v9 - Custom fields re-enabled with ACF plugin
add_filter('acf/settings/remove_wp_meta_box', '__return_false');

// v10 - Remove the Create with Wordpress elements in the Admin page
function wpse_edit_footer() {
    add_filter( 'admin_footer_text', 'wpse_edit_text', 11 );
}

function wpse_edit_text($content) {
    return "Powered by TheAir.Works";
}

add_action( 'admin_init', 'wpse_edit_footer' );

//v13 - github embed access token filter

add_filter( 'github-embed-access-token', function ( $token ) {
	return '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';
} );

add_filter( 'github-embed-access-token-username', function ( $token ) {
	return 'jj-DC';
} );
