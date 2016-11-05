<?php 

namespace Jsc_dont_waste;

class Init {

    
    function __construct(){
        $this->setup();
    }

    /*Registers Post type, taxonomy, and menus */
    public function setup(){
        add_action( 'init', array( $this, 'register_activity_post_type' ) );
        add_action( 'init', array( $this, 'register_activities_taxonomy' ) );
        add_action( 'admin_menu', array( $this, 'add_menus' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        //wp_enqueue_style( 'dontwaste_styles', get_stylesheet_uri() . '/styles/dontwaste_styles.css' );
    }

    //Shouldnt this be able to be private since I'm only calling it from the object itself?
    function register_activity_post_type(){

        $taxonomy_args = array('jsc_activity_cat');

        $labels = array(
            'name'         => __('Activities', 'jsc-dont-waste'),
            'edit_item'    => __('Edit Activity', 'jsc-dont-waste'),
            'add_new_item' => __('Add New Activity', 'jsc-dont-waste'),
            'view_item'    => __('View Activity', 'jsc-dont-waste'),
            );

        $args = array(
            'labels'        => $labels,
            'name'          => __('activities', 'jsc-dont-waste'),
            'taxonomies'    => $taxonomy_args,
            'public'        => true,
            'singular_name' => __('activity', 'jsc-dont-waste'),
            'show_ui'       => true,
            'show_in_menu'  => true
        );

        register_post_type( 'jsc_activity', $args );
    }

    function register_activities_taxonomy(){

        $labels = array(
            'name'          => __('Activity Categories',        'jsc-dont-waste'),
            'singular_name' => __('Activity Category',          'jsc-dont-waste'),
            'search_items'  => __('Search Categories',          'jsc-dont-waste'),
            'all_items'     => __('All Activity Categories',    'jsc-dont-waste'),
            'edit_item'     => __('Edit Activity Category',     'jsc-dont-waste'),
            'update_item'   => __('Update Activity Category',   'jsc-dont-waste'),
            'add_new_item'  => __('Add New Activity Category',  'jsc-dont-waste'),
            'new_item_name' => __('New Activity Category Name', 'jsc-dont-waste'),
            'menu_name'     => __('Activity Category',          'jsc-dont-waste'),
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
            __('Dont waste your life', 'jsc-dont-waste'),
            __('dont waste it!', 'jsc-dont-waste'),
            'manage_options',
            'no_waste',
            array($this, 'jsc_add_menu_cb')
            );
    }

    function register_plugin_styles(){
        wp_register_style( 'dontwaste', JSC_PLUGIN_ASSETS_URL . '/dontwaste_styles.css' );
        wp_enqueue_style( 'dontwaste', false, null, null );
    }

    function jsc_add_menu_cb(){
        include( JSC_DONT_WASTE . '/searchActivities.php' );
    }

}

?>