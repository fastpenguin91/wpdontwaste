    <?php
/*
Plugin Name: dont waste your life
*/




//Including initialization class that adds menus, taxonomies, etc... General plugin setup
include plugin_dir_path( __FILE__ ) . 'models/Init.php';

//Allows plugin to connect with database to get information
include plugin_dir_path( __FILE__ ) . 'models/DataObject.php';

//Defining Root path
define('JSC_DONT_WASTE', __DIR__ );

$plugin_init = new Jsc_dont_waste\Init();

$searchObj = new Jsc_dont_waste\DataObject();
