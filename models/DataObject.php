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

        $this->postIDArray = array();

        foreach ($pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

        $this->get_random_activity();

        //$randomActivityID = array_rand($postIDArray);



        echo "<h1>Here is your Random Activity: </h1>";

        //$randomPost = get_post( $postIDArray[$randomActivityID] );

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

    public function meet_any_conditions(){
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

        $this->postIDArray = array();

        foreach ($pageposts as $obj) {
            $arr = get_object_vars($obj);

            array_push($this->postIDArray, $arr['ID']);
        }

        $this->get_random_activity();

        echo "<h1>Here is your Random Activity: ". $this->randomActivityID . " </h1>";

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

    }

    public function get_random_activity(){
        $arrayElem = array_rand($this->postIDArray);
        $this->randomActivityID = $this->postIDArray[$arrayElem];
    }

    public function display_content(){
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

}

?>