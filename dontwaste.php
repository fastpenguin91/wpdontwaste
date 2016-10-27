    <?php
/*
Plugin Name: dont waste your life
*/

add_action( 'init', 'jsc_register_activities' );
add_action( 'init', 'jsc_register_activities_taxonomy' );
add_action( 'admin_init', 'jsc_add_menus' );


// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_book_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_book_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Genres', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Genres', 'textdomain' ),
        'all_items'         => __( 'All Genres', 'textdomain' ),
        'parent_item'       => __( 'Parent Genre', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Genre:', 'textdomain' ),
        'edit_item'         => __( 'Edit Genre', 'textdomain' ),
        'update_item'       => __( 'Update Genre', 'textdomain' ),
        'add_new_item'      => __( 'Add New Genre', 'textdomain' ),
        'new_item_name'     => __( 'New Genre Name', 'textdomain' ),
        'menu_name'         => __( 'Genre', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre' ),
    );

    register_taxonomy( 'genre', array( 'jsc_activity' ), $args );
    
}


function jsc_register_activities_taxonomy(){

    $labels = array(
        'name'          => 'Activity Categories',
        'singular_name' => 'Activity Category',
        'search_items'  => 'Search Categories',
        'all_items'     => 'All Activity Categories',
        'edit_item'     => 'Edit Activity Category',
        'update_item'   => 'Update Activity Category',
        'add_new_item'  => 'Add New Activity Category',
        'new_item_name' => 'New Activity Category Name',
        'menu_name'     => 'Activity Category',
        );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        /*'show_admin_column' => true,*/
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre' ),
    );


    register_taxonomy( 'jsc_activity_cat', 'jsc_activity', $args );
}

function jsc_register_activities(){

    $taxonomy_args = array('jsc_activity_cat');

    $labels = array(
        'name'         => 'Activities',
        'edit_item'    => 'Edit Activity',
        'add_new_item' => 'Add New Activity',
        'view_item'    => 'View Activity',
        );

    $args = array(
        'labels'        => $labels,
        'name'          => 'activities',
        'taxonomies'    => $taxonomy_args,
        'public'        => true,
        'singular_name' => 'activity',
        'show_ui'       => true,
        'show_in_menu'  => true
    );


    register_post_type( 'jsc_activity', $args );
}

function jsc_add_menus(){
    add_menu_page('Dont waste your life', 'dont waste it!', 'manage_options', 'no_waste');
}