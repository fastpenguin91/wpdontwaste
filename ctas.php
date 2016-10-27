    <?php
/*
Plugin Name: dont waste your life
*/

add_action('admin_init', 'jsc_add_menus' );


function jsc_add_menus(){
    add_menu_page('Dont waste your life', 'dont waste it!', 'manage_options', 'no_waste');
}