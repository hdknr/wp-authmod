<?php
namespace Authmod;

class Admin extends Base {

    function __construct( array $argument = array() ){
    }

    function options_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $data = \Timber::get_context();
        $data['posts'] = \Timber::get_posts();
        $data['foo'] = 'bar';
        \Timber::render('Admin.html', $data);    
    }
}
