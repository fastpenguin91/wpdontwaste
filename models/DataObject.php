<?php

namespace Jsc_dont_waste;

class DataObject {

    public $postIDArray = array();
    public $randomActivityID = '';

    public function setup(){
        add_action( 'admin_post_nopriv_meet_all_conditions', array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_meet_all_conditions',        array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_nopriv_meet_any_conditions', array( $this, 'meet_any_conditions' ) );
        add_action( 'admin_post_meet_any_conditions',        array( $this, 'meet_any_conditions' ) );
    }

    public function meet_all_conditions() {

        $pageposts = $this->all_conditions_search();

        $this->postIDArray = array();

        foreach ($pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

        $this->get_random_activity();

        $this->display_content();

    }

    public function meet_any_conditions(){
        
        $pageposts = $this->any_condition_search();
        

        $this->postIDArray = array();

        foreach ($pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

        $this->get_random_activity();

        $this->display_content();
    }

    public function all_conditions_search(){
        global $wpdb;

        $categorySelection = '';
        $activitiesCount = 0;
        $numItems = count($_GET['activities']);

        foreach( $_GET['activities'] as $value) {
            $categorySelection .= $value;
            $categoryCount += 1;

            if ($categoryCount !== $numItems) {
                $categorySelection .= ', ';
            }
        }

        $querystr = "SELECT wp_posts.post_title, wp_posts.ID, wp_terms.name FROM wp_posts
            LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
            LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
            WHERE post_type = 'jsc_activity' AND wp_term_relationships.term_taxonomy_id IN (" . $categorySelection . ")
            GROUP BY wp_posts.ID
            HAVING COUNT(*) =" . $categoryCount;

        return $wpdb->get_results($querystr, OBJECT);

    }

    public function any_condition_search(){
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


        return $wpdb->get_results($sqlQuery, OBJECT);
    }

    public function get_random_activity(){
        $arrayElem = array_rand($this->postIDArray);
        $this->randomActivityID = $this->postIDArray[$arrayElem];
    }

    public function display_content(){
        get_header();

        echo "<h1>Here is your Random Activity: </h1>";
        
        $randomPost = get_post( $this->randomActivityID );

        echo "<h3>" . $randomPost->post_title . "</h3><br>";

        echo $randomPost->post_content;

        echo "<br><br><br><h2>Here are all of the activities that fit your criteria: </h2><br><br><br>";

        $args = array(
            'include' => $this->postIDArray,
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

}

?>