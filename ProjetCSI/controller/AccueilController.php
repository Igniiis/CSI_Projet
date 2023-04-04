<?php

class AccueilController extends Controller{


    function view($id){

        echo 'flop';
    }

    function index(){
        $this->render('/accueil');
    }

    function admin_index(){
        $this->index();
    }


}