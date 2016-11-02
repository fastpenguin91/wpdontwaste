<?php

namespace Jsc_dont_waste;

/*Only two methods in this class actually deal with processing data from the database.
The rest manipulates and prepares that data to be displayed in a view, so I think this class needs to be split in two
but then the two classes will be "tightly coupled" because the view class would rely on this DataObject class?
*/

class DataObject {

    /*********
    This is the Content Class instantiation.
    *********/
    public $contentObj;

    function __construct($contentObj){
        $this->setup();
        $this->contentObj = $contentObj;
    }

    //Connect the search form callback functions to the function that needs to be called when the form is submitted.
    //ex. meet_all_conditions is called when the meet_all_conditions form is submitted.
    public function setup(){
        add_action( 'admin_post_nopriv_meet_all_conditions', array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_meet_all_conditions',        array( $this, 'meet_all_conditions' ) );
        add_action( 'admin_post_nopriv_meet_any_conditions', array( $this, 'meet_any_conditions' ) );
        add_action( 'admin_post_meet_any_conditions',        array( $this, 'meet_any_conditions' ) );
    }
    
    public function meet_all_conditions() {
        $this->condition_search("all");
        $this->contentObj->prepare_results();
    }

    public function meet_any_conditions(){
        $this->condition_search("any");
        $this->contentObj->prepare_results();
    }

    public function condition_search($searchType){
        global $wpdb;

        $categorySelection = '';
        $categoryCount = 0;
        $numItems = count($_GET['activities']);

        if ( $searchType == "any" ) {
            foreach( $_GET['activities'] as $value) {
                if ($categoryCount == 0) {
                    $categorySelection .= ' wp_terms.term_id = ' . $value;
                    $categoryCount += 1;
                } else if ($categoryCount !== $numItems ) {
                    $categorySelection .= ' OR wp_terms.term_id = ' . $value;
                }
            }

            $categorySelection .= " )";

            $queryStr = "SELECT DISTINCT wp_posts.ID FROM wp_posts
            LEFT JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
            LEFT JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
            WHERE post_type = 'jsc_activity' AND ( " . $categorySelection;

        } else if ( $searchType == "all" ) {
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
        }

        $this->contentObj->pageposts = $wpdb->get_results($queryStr, OBJECT);
    }
    
}

?>