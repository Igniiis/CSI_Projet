<?php

class EclairagesController extends Controller{

    function index(){
        $this->loadModel('eclairage');
        $this->Eclairage = new Eclairage();

        //si on a post
        if($this->request->data){
            $this->push();
        }

        //cette partie est la pour donner accès au nom des rues
        $this->loadModel('rue');
        $this->Rue = new Rue();
        $d['rues'] = $this->Rue->find(array());
        $this->set($d);
    }


    function admin_index(){
        $d = array();

        $this->loadModel('eclairage');
        $this->Eclairage = new Eclairage();

        //si on a post
        if($this->request->data){
            $this->push();
        }

        //pour l'administrateur, il peut voir des stats global
        if($this->isAdmin()){
            $d['eclairages'] = $this->Eclairage->find(array());
            $d['administrateurMode']=true;
        }

        //cette partie est la pour donner accès au nom des rues
        $this->loadModel('rue');
        $this->Rue = new Rue();
        $d['rues'] = $this->Rue->find(array());
        $this->set($d);
    }

    public function isAdmin(){
        if(isset($_SESSION['Agent']->type_agent)){
            if($_SESSION['Agent']->type_agent=='responsable'){
                return true;
            }
        }
        return false;
    }

    function push(){

    }


}