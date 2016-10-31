<?php 

namespace Jsc_dont_waste;

class Init {

    /*Registers Post type, taxonomy, and menus */
    public function setup(){
        add_action( 'init', array( $this, 'register_activity_post_type' ) );
        add_action( 'init', array( $this, 'register_activities_taxonomy' ) );
        add_action( 'admin_menu', array( $this, 'add_menus' ) );
    }


    //Shouldnt this be able to be private since I'm only calling it from the object itself?
    function register_activity_post_type(){

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

    function register_activities_taxonomy(){

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
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'genre' ),
        );


        register_taxonomy( 'jsc_activity_cat', 'jsc_activity', $args );
    }

    function add_menus(){
        add_menu_page(
            'Dont waste your life',
            'dont waste it!',
            'manage_options',
            'no_waste',
            array($this, 'jsc_add_menu_cb')
            );
    }

    function jsc_add_menu_cb(){
        include( JSC_DONT_WASTE . '/searchActivities.php' );
    }

}



?>