    <?php
/*
Plugin Name: dont waste your life
*/




//Including initialization class that adds menus, taxonomies, etc... General plugin setup
include plugin_dir_path( __FILE__ ) . 'models/Init.php';
//include Content Class
include plugin_dir_path( __FILE__ ) . 'models/Content.php';
//Allows plugin to connect with database to get information
include plugin_dir_path( __FILE__ ) . 'models/DataObject.php';
//Defining Root path
define('JSC_DONT_WASTE', __DIR__ );

$plugin_init = new Jsc_dont_waste\Init();
$contentObj =  new Jsc_dont_waste\Content();
$searchObj =   new Jsc_dont_waste\DataObject($contentObj);
