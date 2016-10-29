    <?php
/*
Plugin Name: dont waste your life
*/

add_action( 'init', 'jsc_register_activities' );
add_action( 'init', 'jsc_register_activities_taxonomy' );
add_action( 'admin_menu', 'jsc_add_menus' );
add_action( 'admin_post_nopriv_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'prefix_send_email_to_admin' );

global $wpdb;

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

function prefix_send_email_to_admin() {

    global $wpdb;
    /**
     * At this point, $_GET/$_POST variable are available
     *
     * We can do our normal processing here
     */

    echo "sent the GET form from INSIDE THE PLUGIN. From the admin area.<br>";

    $activitiesSelection = '';
    $activitiesCount = 0;
    $numItems = count($_GET['activities']);


    foreach( $_GET['activities'] as $value) {
        $activitiesSelection .= $value;
        $activitiesCount += 1;

        if ($activitiesCount !== $numItems) {
            $activitiesSelection .= ', ';
        }


    }

    /*die(var_dump($_GET['activities']));*/

    echo "activities selection: " . $activitiesSelection . "<br><br><br>";


    $querystr = "SELECT wp_posts.post_title, wp_posts.ID, wp_terms.name FROM wp_posts
                LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
                LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
                WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN (" . $activitiesSelection . ")
                GROUP BY wp_posts.ID
                HAVING COUNT(*) =" . $activitiesCount;




                /*"SELECT wp_posts.post_title, wp_posts.ID, wp_terms.name FROM wp_posts
                LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
                LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
                WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN ('4', '3', '2')
                GROUP BY wp_posts.ID
                HAVING COUNT(*) = 3";*/

                


    /*$querystr = "
    SELECT *
    FROM $wpdb->posts
    WHERE post_type = 'jsc_activity'";*/

    /*echo '<br>' . $querystr;*/


    $pageposts = $wpdb->get_results($querystr, OBJECT);

    var_dump($pageposts);


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
    add_menu_page(
        'Dont waste your life',
        'dont waste it!',
        'manage_options',
        'no_waste',
        'jsc_add_menu_cb'
        );
}

function jsc_add_menu_cb(){
    include( plugin_dir_path(__FILE__) . 'searchActivities.php' );
}



function myCustomSearchQuery(){
    $querystr = "
    SELECT post_title
    FROM $wpdb->posts
    WHERE post_type = 'jsc_activity'";

    /*$querystr = "
        SELECT $wpdb->posts.* 
        FROM $wpdb->posts";/*, $wpdb->postmeta
        WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
        AND $wpdb->postmeta.meta_key = 'tag' 
        AND $wpdb->postmeta.meta_value = 'email' 
        AND $wpdb->posts.post_status = 'publish' 
        AND $wpdb->posts.post_type = 'post'
        AND $wpdb->posts.post_date < NOW()
        ORDER BY $wpdb->posts.post_date DESC
     ";*/

     /*Original SQL Statement. $querystr = "
        SELECT $wpdb->posts.* 
        FROM $wpdb->posts, $wpdb->postmeta
        WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
        AND $wpdb->postmeta.meta_key = 'tag' 
        AND $wpdb->postmeta.meta_value = 'email' 
        AND $wpdb->posts.post_status = 'publish' 
        AND $wpdb->posts.post_type = 'post'
        AND $wpdb->posts.post_date < NOW()
        ORDER BY $wpdb->posts.post_date DESC
     "; */

     $pageposts = $wpdb->get_results($querystr, OBJECT);

    die(var_dump($pageposts) );
}