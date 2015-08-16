<?php
namespace Authmod;

class App extends AppBase {
    protected $admin = null;
    protected $option = null;

    function __construct(array $argument = array()) {
        parent::__construct($argument);
        $this->admin = Admin::get_instance();
        $this->option = Option::get_instance();
    }    

    function verify_hash($session_key, $id, $hash){
        if($hash == '') {
            return true;
        }
        $alg = $this->option->hashalg_name;
        $key = $this->option->shared_secret;
        $res = $hash == hash($alg, "$session_key$id$key");
        return $res;
    }

    function get_session_key(){
        $name = $this->option->sessionid_name;
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    function get_wp_user(){
        $name = $this->option->usertoken_name;
        $wp_user = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
        preg_match("/(?P<id>\d+):(?P<hash>[0-9a-f]*)/", $wp_user, $match);
        return $match == null ? array(null, null) : array($match['id'], $match['hash']);
    }

    function force_auth(){

        list($id, $hash) = $this->get_wp_user();

        // https://codex.wordpress.org/Function_Reference/is_user_logged_in
        if(is_user_logged_in()){
            if($id == null){
                // https://codex.wordpress.org/Function_Reference/wp_logout
                wp_logout();
                return ;
            }
        } else { 
            if(($session_key = $this->get_session_key()) != null ){
                if($this->verify_hash($session_key, $id, $hash)){
                    if(($user = get_user_by('id', $id)) != null){
                        wp_set_current_user($id, $user->user_login);
                        wp_set_auth_cookie($id);
                        do_action('wp_login', $user->user_login);
                    }
                }
            }
        }
    }

    function action_send_headers(){
        // https://codex.wordpress.org/Plugin_API/Action_Reference/send_headers
        error_log("action:send_header:");
        $this->force_auth();        // TODO: seek proper action 
    }

    function action_admin_menu(){
        add_options_page(
            'wp-authmod options', 
            'wp-authmod', 
            'manage_options', 
            'wp-authmod', 
            array($this->admin, 'options_page'));
    }
}
