<?php

class PagesController extends Controller{


    function view($id){
        $this->loadModel('Personne');
        
        $d['personne'] = $this->Personne->findFirst(array(
            'conditions' => array('id'=>$id)
        ));

        //si la requête ne retourne aucune ligne :
        if(empty($d['personne'])){
            $this->e404('Page introuvable');
        }

        $d['personnes'] = $this->Personne->find(array());

        $this->set($d);
    }


    /**
    function view($nom){
        //si il n'y a qu'une seul variable à set :
        $this->set('phrase','Bienvenue sur la page '.$nom) ;

        //si il y en a plusieurs :
        $this->set(array(
            'phrase' => 'Bienvonido sura la paga '.$nom,
            'prenom' => 'Miguel',
            'nom' => 'Castro'
        ));


        $this->render('index');
    } 
    */

}