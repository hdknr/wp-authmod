<?php
namespace Authmod;

class Option extends Base {
    protected $key = "wp-authmod";
    protected $option = array(
        'shared_secret' => '',
        'sessionid_name' => 'sessionid',
        'usertoken_name' => 'WP_USER',
        'hashalg_name' => 'sha256',
    );

    function __construct( array $argument = array() ){
        $opt = get_option($this->key, array());
        if ($opt != null){
            $this->option = $opt;        
        }
    }

    function __get($name){
        if(array_key_exists($name, $this->option)){
            return $this->option[$name];
        }
    }

    function __set($name, $value){
        error_log("__set: $name = $value ");
        if(array_key_exists($name, $this->option)){
            $this->option[$name] = $value;
        } else {
            error_log("__set: $name not found");
        }
    }

    function get(){
        return $this->option; 
    }

    function update($values=null){
        if($values != null){
            foreach($values as $k => $v ){
                $this->option[$k] = $v;
            }
        }
        update_option($this->key, $this->option);
    }
}
