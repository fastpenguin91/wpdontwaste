    <?php
/*
Plugin Name: dont waste your life
*/




add_action( 'admin_post_nopriv_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'prefix_send_email_to_admin' );
add_action( 'admin_post_nopriv_random_or', 'prefix_or_search' );
add_action( 'admin_post_random_or', 'prefix_or_search' );

include plugin_dir_path(__FILE__) . 'models/init.php';

define('JSC_DONT_WASTE', __DIR__ );

$plugin_init = new Jsc_dont_waste\Init();

$plugin_init->setup();

function prefix_or_search(){
    global $wpdb;

    $chosenCategories = '';

    $activitiesSelection = '';
    $activitiesCount = 0;
    $numItems = count($_GET['activities']);

    foreach( $_GET['activities'] as $value) {

        if ($activitiesCount == 0) {
            $activitiesSelection .= ' wp_terms.term_id = ' . $value;
            $activitiesCount += 1;
        } else if ($activitiesCount !== $numItems ) {
            $activitiesSelection .= ' OR wp_terms.term_id = ' . $value;
        }
    }

    $activitiesSelection .= " )";

    $sqlQuery = "SELECT DISTINCT wp_posts.post_title, wp_posts.ID FROM wp_posts
    LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
    LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
    WHERE post_type = 'jsc_activity' AND ( " . $activitiesSelection;


    $pageposts = $wpdb->get_results($sqlQuery, OBJECT);

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




//Pretty sure this function is NOT being used at all.
function myCustomSearchQuery(){
    $querystr = "
    SELECT post_title
    FROM $wpdb->posts
    WHERE post_type = 'jsc_activity'";

     $pageposts = $wpdb->get_results($querystr, OBJECT);
}