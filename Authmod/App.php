<?php
namespace Authmod;

class App extends Base {

    function __construct(array $argument = array()) {
        $this->_install_hook();
    }      

    function _install_hook(){
        foreach(get_class_methods($this) as $fn){
            preg_match("/^(?P<hook>[^_]+)_(?P<name>.+)$/", $fn, $match);
            if($match != null && 
               in_array($match['hook'], ['action', 'filter'])){
                $hook = "add_".$match['hook'];
                $hook($match['name'], array($this, $fn)); 
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
    }
}
