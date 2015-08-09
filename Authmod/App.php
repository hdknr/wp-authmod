<?php
namespace Authmod;

class App extends Base {

    function __construct(array $argument = array())
    {
    }      

    function filter_the_title($title){
        return "$title (debugging)";
    } 
}
