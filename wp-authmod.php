<?php
/*
Plugin Name: wp-authmod
Version: 0.1-alpha
Description: Wordpress Authentication Module
Author: Hideki Nara
Author URI: https://github.com/hdknr
Plugin URI: https://github.com/hdknr/wp-authmod
Text Domain: wp-authmod
Domain Path: /languages
License: BSD 
*/

add_action('plugins_loaded', function(){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR . "bootstrap.php";
    // $GLOBALS['wp-authmod-plugin'] = 
    $app = ClassLoader::app_instance('\\Authmod\\App'); 
});
