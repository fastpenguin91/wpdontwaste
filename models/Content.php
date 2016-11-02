<?php

namespace Jsc_dont_waste;

class Content {

    public $postIDArray = array();
    public $randomPost = '';
    public $pageposts = '';

    /*
    This method is like a "run" function. It executes all the methods in the correct order. In order to display the content
    */
    public function prepare_results(){
        $this->get_activity_post_ids();
        $this->get_random_activity();
        $this->display_content();
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