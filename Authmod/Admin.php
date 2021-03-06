<?php
namespace Authmod;

class Admin extends Base {

    function __construct( array $argument = array() ){
    }

    function options_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $ctx = \Timber::get_context();

        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $ctx['form'] = Option::get_instance()->get();
        }else{
            $ctx['form'] = $_POST;
            Option::get_instance()->update($_POST);
        }

        \Timber::render('Admin.html', $ctx);    
    }
}
