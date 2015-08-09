<?php
/*
Plugin Name: Wp-authmod
Version: 0.1-alpha
Description: Wordpress Authentication Module
Author: Hideki Nara
Author URI: https://github.com/hdknr
Plugin URI: https://github.com/hdknr/wp-authmod
Text Domain: wp-authmod
Domain Path: /languages
License: BSD 
*/

add_action( 'plugins_loaded', '_bootstrap');

function _bootstrap(){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR . "bootstrap.php";
    $GLOBALS['wp-authmod-plugin'] = _app(); 
}
