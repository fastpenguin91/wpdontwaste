<?php

namespace Jsc_dont_waste;

/*Only two methods in this class actually deal with processing data from the database.
The rest manipulates and prepares that data to be displayed in a view, so I think this class needs to be split in two
but then the two classes will be "tightly coupled" because the view class would rely on this DataObject class?
*/

class DataObject {

    /*********
    These properties are changed by both search forms and used in most methods. I'm not sure if it's better to scope these variables within
    the methods that call them or if they are better as class properties, or if there's some other better way.
    *********/
    public $postIDArray = array();
    public $randomPost = '';
    public $pageposts = '';

    function __construct(){
        $this->setup();
    }

    //Connect the search form callback functions to the function that needs to be called when the form is submitted.
    //ex. meet_all_conditions is called when the meet_all_conditions form is submitted.
    public function setup(){
        add_action( 'admin_post_nopriv_meet_all_conditions', array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_meet_all_conditions',        array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_nopriv_meet_any_conditions', array( $this, 'meet_any_conditions' ) );
        add_action( 'admin_post_meet_any_conditions',        array( $this, 'meet_any_conditions' ) );
    }

    /*
    I could get rid of this method and just call $this->prepare_results() at the end of the all_conditions_search() method,
    but that looks wrong to me.
    */
    public function meet_all_conditions() {

        $this->all_conditions_search(); //data

        $this->prepare_results();

    }

    /*
    I could get rid of this method and just call $this->prepare_results() at the end of the any_conditions_search() method,
    but that looks wrong to me.
    */
    public function meet_any_conditions(){
        
        $this->any_condition_search(); //data

        $this->prepare_results();

    }


    /*
    This method is like a "run" function. It executes all the methods in the correct order. In order to display the content
    */
    public function prepare_results(){
        $this->get_activity_post_ids();

        $this->get_random_activity();

        $this->display_content();
    }


    //Loads the search POST ID's into the $pageposts array property
    //This method is very similar to the "any_condition_search" method so I'm not sure if I should leave it as it is
    //or do something different with these functions.
    public function all_conditions_search(){
        global $wpdb;

        $categorySelection = '';
        $categoryCount = 0;
        $numItems = count($_GET['activities']);

        foreach( $_GET['activities'] as $value) {
            $categorySelection .= $value;
            $categoryCount += 1;

            if ($categoryCount !== $numItems) {
                $categorySelection .= ', ';
            }
        }

        $queryStr = "SELECT wp_posts.ID FROM wp_posts
            LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
            LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
            WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN (" . $categorySelection . ")
            GROUP BY wp_posts.ID
            HAVING COUNT(*) =" . $categoryCount;

        $this->pageposts = $wpdb->get_results($queryStr, OBJECT);
    }

    public function any_condition_search(){
        global $wpdb;

        $categorySelection = '';
        $categoryCount = 0;
        $numItems = count($_GET['activities']);

        foreach( $_GET['activities'] as $value) {

            if ($categoryCount == 0) {
                $categorySelection .= ' wp_terms.term_id = ' . $value;
                $categoryCount += 1;
            } else if ($categoryCount !== $numItems ) {
                $categorySelection .= ' OR wp_terms.term_id = ' . $value;
            }
        }

        $categorySelection .= " )";

        $sqlQuery = "SELECT DISTINCT wp_posts.ID FROM wp_posts
        LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
        LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
        WHERE post_type = 'jsc_activity' AND ( " . $categorySelection;


        $this->pageposts = $wpdb->get_results($sqlQuery, OBJECT);
    }


    //makes the search results a little easier to work with. Instead of an array of objects, it becomes an array of ID's
    //so I can get each post by its id.
    public function get_activity_post_ids(){
        $this->postIDArray = array();

        foreach ($this->pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

    }

    //sets $randomPost property to a random post that matches the users criteria.
    public function get_random_activity(){
        $arrayElem = array_rand($this->postIDArray);
        $randomActivityID = $this->postIDArray[$arrayElem];
        $this->randomPost = get_post( $randomActivityID );
    }

    //displays the results page
    public function display_content(){
        get_header();

        echo $this->display_random_post();

        echo $this->display_all_posts();

        get_footer();
    }

    //prepares the random post for display
    public function display_random_post(){
        $randomPostStr = '';

        $randomPostStr .= "<h1>Here is your Random Activity: </h1>";

        $randomPostStr .= "<h3>" . $this->randomPost->post_title . "</h3><br>";

        $randomPostStr .= $this->randomPost->post_content;

        return $randomPostStr;
    }

    //prepares all posts that fit criteria for display
    public function display_all_posts(){

        $allPostsStr = '<br><br><br><h2>Here are all of the activities that fit your criteria: </h2><br><br><br>';


        $args = array(
            'include' => $this->postIDArray,
            'post_type'   => 'jsc_activity'
        );

        $listOfPosts = get_posts( $args );

        foreach($listOfPosts as $post ) {
            $allPostsStr .= "<h2>" . $post->post_title . '</h2>';
            $allPostsStr .= $post->post_content;
            $allPostsStr .= "<br>";
        }

        return $allPostsStr;
    }
}

?>