<?php

class AccueilController extends Controller{


    function index(){

        $this->render('/accueil');
    }

    function succes(){
        $this->Session->setFlash("Signalement envoyé.");
        $this->render('/accueil');
    }

    function admin_index(){
        $d['name'] = $this->request->data;
        $this->render('/accueil');
    }


}