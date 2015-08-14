<?php
namespace Authmod;

class App extends AppBase {

    function force_auth(){
        if(!isset($_COOKIE['WP_USER'])){
            return;
        }
        $wp_user = $_COOKIE['WP_USER'];
        preg_match("/(?P<id>\d+):(?P<hash>[0-9a-f]*)/", $wp_user, $match);
        if($match != null){
            // TODO: Verify 'hash' if it is given with shared key
            $user = get_user_by( 'id', $match['id']); 
            if($user){
                wp_set_current_user($match['id'], $user->user_login );
                wp_set_auth_cookie($match['id']);
                do_action( 'wp_login', $user->user_login );
            }
        }
    }

    function filter_the_title($title){
        error_log("filter:the_title");
        return "$title (debugging)";
    } 

    function filter_query_vars($qvars){
        error_log("filter:query_vars");
        return $qvars + array('authmod');
    }

    function action_init(){
        error_log("action:init");
    }

    function action_template_redirect(){
        error_log("action:template_redirect");
    }

    function action_pre_get_posts(){
        error_log("action:pre_get_posts");
        $this->force_auth();        // TODO: seek proper action 
    }
}
