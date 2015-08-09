<?php
namespace Authmod;

class App extends Base {

    protected $_filters = [
        'the_title', 'query_vars', ];

    function __construct(array $argument = array()) {
        foreach($this->_filters as $filter){
            add_filter($filter, array($this, "filter_" . $filter));
        }
    }      

    function filter_the_title($title){
        return "$title (debugging)";
    } 

    function filter_query_vars($qvars){
        var_dump($qvars);
        return $qvars;
    }
}
