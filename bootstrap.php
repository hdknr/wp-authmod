<?php

class ClassLoader
{
    public static function loadClass($class)
    {
        error_log("loadClass($class)");

        foreach (self::directories() as $directory) {
            $file_name = str_replace(
                "\\", "/", 
                $directory.DIRECTORY_SEPARATOR.$class.".php");

            if (is_file($file_name)) {
                require $file_name;
                return true;
            }
        }

    }

    private static $dirs;

    private static function directories()
    {
        if (empty(self::$dirs)) {
            $base = __DIR__;
            self::$dirs = array(
                $base,
            );
        }

        return self::$dirs;
    }

    public static function app_instance($appclass_name){
        $timberlib = WP_PLUGIN_DIR . '/timber-library/timber.php';
        if(is_file($timberlib)){
            require_once $timberlib;
        }
        spl_autoload_register(array('ClassLoader', 'loadClass'));
        return call_user_func(array($appclass_name, 'get_instance'));
    }
}
