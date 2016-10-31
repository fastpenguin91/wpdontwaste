    <?php
/*
Plugin Name: dont waste your life
*/





include plugin_dir_path( __FILE__ ) . 'models/Init.php';
include plugin_dir_path( __FILE__ ) . 'models/DataObject.php';

define('JSC_DONT_WASTE', __DIR__ );

$plugin_init = new Jsc_dont_waste\Init();

$plugin_init->setup();

$searchObj = new Jsc_dont_waste\DataObject();

$searchObj->setup();


