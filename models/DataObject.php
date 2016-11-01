<?php

namespace Jsc_dont_waste;

class DataObject {

    public $postIDArray = array();
    public $randomPost = '';
    public $pageposts = '';

    public function setup(){
        add_action( 'admin_post_nopriv_meet_all_conditions', array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_meet_all_conditions',        array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_nopriv_meet_any_conditions', array( $this, 'meet_any_conditions' ) );
        add_action( 'admin_post_meet_any_conditions',        array( $this, 'meet_any_conditions' ) );
    }

    public function meet_all_conditions() {

        $this->all_conditions_search();

        $this->prepare_results();

    }

    public function meet_any_conditions(){
        
        $this->any_condition_search();

        $this->prepare_results();

    }

    public function prepare_results(){
        $this->get_activity_post_ids();

        $this->get_random_activity();

        $this->display_content();
    }

    public function get_activity_post_ids(){
        $this->postIDArray = array();

        foreach ($this->pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

        die(var_dump($this->postIDArray));
    }

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

        $querystr = "SELECT wp_posts.ID FROM wp_posts
            LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
            LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
            WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN (" . $categorySelection . ")
            GROUP BY wp_posts.ID
            HAVING COUNT(*) =" . $categoryCount;

        $this->pageposts = $wpdb->get_results($querystr, OBJECT);

        die(var_dump($this->pageposts));

    }

    public function generate_query_string($userSelection){
        $categorySelection = '';
        $categoryCount = 0;
        $numItems = count($_GET['activities']);
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

    public function get_random_activity(){
        $arrayElem = array_rand($this->postIDArray);
        $randomActivityID = $this->postIDArray[$arrayElem];
        $this->randomPost = get_post( $randomActivityID );
    }

    public function display_content(){
        get_header();

        echo "<h1>Here is your Random Activity: </h1>";
        
        echo "<h3>" . $this->randomPost->post_title . "</h3><br>";

        echo $this->randomPost->post_content;

        echo "<br><br><br><h2>Here are all of the activities that fit your criteria: </h2><br><br><br>";

        echo $this->display_all_posts();

        get_footer();
    }

    public function display_all_posts(){

        $allPostsStr = '';


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