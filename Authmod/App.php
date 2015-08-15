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
                do_action('wp_login', $user->user_login);
            }
        }
        else {
            // https://codex.wordpress.org/Function_Reference/is_user_logged_in
            // https://codex.wordpress.org/Function_Reference/wp_logout
            if(is_user_logged_in()){
                wp_logout();
            }
        }
    }

    function action_send_headers(){
        // https://codex.wordpress.org/Plugin_API/Action_Reference/send_headers
        error_log("action:send_header:");
        $this->force_auth();        // TODO: seek proper action 
    }
}
