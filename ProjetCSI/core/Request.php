<?php

class Request{

    public $url;   //URL appelé par l'utilisateur

    public $data = false;
    public $prefix = false;

    function __construct(){

        if(isset($_SERVER['PATH_INFO'])){
            $this->url = $_SERVER['PATH_INFO'];
        }else{
            $this->url = '/';
        }

        if(!empty($_POST)){
            $this->data = new stdClass();
            foreach($_POST as $k=>$v){
                $this->data->$k = $v;
            }
            
        }
    }
}

?>