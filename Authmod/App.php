<?php
namespace Authmod;

class App extends AppBase {

    function verify_hash($session_key, $id, $hash){
        if($hash == '') {
            return true;
        }
        $alg = "sha256";                    // TODO: configuration
        $key = 'this is the secret.';       // TODO: configuration
        $res = $hash == hash($alg, "$session_key$id$key");
        return $res;
    }

    function get_session_key(){
        $name = 'sessionid';                // TODO: configuration
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    function get_wp_user(){
        $name = 'WP_USER';                  // TODO: configuration
        $wp_user = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
        preg_match("/(?P<id>\d+):(?P<hash>[0-9a-f]*)/", $wp_user, $match);
        return $match == null ? array(null, null) : array($match['id'], $match['hash']);
    }

    function force_logout(){
        // https://codex.wordpress.org/Function_Reference/is_user_logged_in
        // https://codex.wordpress.org/Function_Reference/wp_logout
        if(is_user_logged_in()){
            wp_logout();
        }
    }

    function force_auth(){

        list($id, $hash) = $this->get_wp_user();

        if($id == null){
            $this->force_logout();
            return ;
        }

        if(($session_key = $this->get_session_key()) != null ){

            if(!$this->verify_hash($session_key, $id, $hash)){
                $this->force_logout();
                return;
            }
    
            if(($user = get_user_by('id', $id)) != null){
                wp_set_current_user($id, $user->user_login);
                wp_set_auth_cookie($id);
                do_action('wp_login', $user->user_login);
            }
        }
    }

    function action_send_headers(){
        // https://codex.wordpress.org/Plugin_API/Action_Reference/send_headers
        error_log("action:send_header:");
        $this->force_auth();        // TODO: seek proper action 
    }
}
