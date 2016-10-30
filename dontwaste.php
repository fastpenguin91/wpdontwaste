    <?php
/*
Plugin Name: dont waste your life
*/

add_action( 'init', 'jsc_register_activities' );
add_action( 'init', 'jsc_register_activities_taxonomy' );
add_action( 'admin_menu', 'jsc_add_menus' );
add_action( 'admin_post_nopriv_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_nopriv_random_or', 'prefix_or_search' );
add_action( 'admin_post_random_or', 'prefix_or_search' );

function prefix_or_search(){
    global $wpdb;
    /*echo "Or Search complete yo";*/

    $chosenCategories = '';

    /*var_dump($_GET['activities']);*/


    $activitiesSelection = '';
    $activitiesCount = 0;
    $numItems = count($_GET['activities']);

    foreach( $_GET['activities'] as $value) {
        /*$activitiesSelection .= " OR wp_terms.term_id = " . $value;*/

        //echo "<br>This is the value: " . var_dump($value);

        if ($activitiesCount == 0) {
            $activitiesSelection .= ' wp_terms.term_id = ' . $value;
            $activitiesCount += 1;
        } else if ($activitiesCount !== $numItems ) {
            $activitiesSelection .= ' OR wp_terms.term_id = ' . $value;
        }

    }

    $activitiesSelection .= " )";

    /*echo "activities Selection var is: " . $activitiesSelection;*/

    $sqlQuery = "SELECT DISTINCT wp_posts.post_title, wp_posts.ID FROM wp_posts
    LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
    LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
    WHERE post_type = 'jsc_activity' AND ( " . $activitiesSelection;

    //This query works but leaves in Auto Drafts.
    /*$sqlQuery = "SELECT DISTINCT wp_posts.post_title, wp_posts.ID FROM wp_posts
    LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
    LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
    WHERE post_type = 'jsc_activity'" . $activitiesSelection;*/

    $pageposts = $wpdb->get_results($sqlQuery, OBJECT);

    /*die($sqlQuery);*/

    /*die(var_dump($pageposts));*/


    $postIDArray = array();

    foreach ($pageposts as $obj) {
        $arr = get_object_vars($obj);

        /*echo var_dump($arr) . "<br>";*/

        array_push($postIDArray, $arr['ID']);
    }

    /*echo var_dump($postIDArray);*/

    $randomActivityID = array_rand($postIDArray);

    echo "<h1>Here is your Random Activity: </h1>";

    $randomPost = get_post( $postIDArray[$randomActivityID] );

    echo "<h3>" . $randomPost->post_title . "</h3><br>";

    echo $randomPost->post_content;

    echo "<br><br><br><h2>Here are all of the activities that fit your criteria: </h2><br><br><br>";

    $args = array(
        'include' => $postIDArray,
        'post_type'   => 'jsc_activity'
    );

    $listOfPosts = get_posts( $args );

    foreach($listOfPosts as $post ) {
        echo "<h2>" . $post->post_title . '</h2>';
        echo $post->post_content;
        echo "<br>";
    }




}

function prefix_send_email_to_admin() {

    global $wpdb;

    get_header();

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

    $querystr = "SELECT wp_posts.post_title, wp_posts.ID, wp_terms.name FROM wp_posts
                LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
                LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
                WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN (" . $activitiesSelection . ")
                GROUP BY wp_posts.ID
                HAVING COUNT(*) =" . $activitiesCount;


    /*$querystr = "
    SELECT *
    FROM $wpdb->posts
    WHERE post_type = 'jsc_activity'";*/

    $pageposts = $wpdb->get_results($querystr, OBJECT);

    $postIDArray = array();

    foreach ($pageposts as $obj) {
        $arr = get_object_vars($obj);

        array_push($postIDArray, $arr['ID']);
    }

    $randomActivityID = array_rand($postIDArray);

    echo "<h1>Here is your Random Activity: </h1>";

    $randomPost = get_post( $postIDArray[$randomActivityID] );

    echo "<h3>" . $randomPost->post_title . "</h3><br>";

    echo $randomPost->post_content;

    echo "<br><br><br><h2>Here are all of the activities that fit your criteria: </h2><br><br><br>";

    $args = array(
        'include' => $postIDArray,
        'post_type'   => 'jsc_activity'
    );

    $listOfPosts = get_posts( $args );

    foreach($listOfPosts as $post ) {
        echo "<h2>" . $post->post_title . '</h2>';
        echo $post->post_content;
        echo "<br>";
    }

    get_footer();


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