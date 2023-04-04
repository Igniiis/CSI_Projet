<?php

class EclairagesController extends Controller{

    function index(){
        $this->loadModel('eclairage');
        $this->Eclairage = new Eclairage();

        //si on a post
        if($this->request->data){
            $this->push($this->Eclairage);
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
            $this->push($this->Eclairage);
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

    /**
     * Méthode qui execute la/les requêtes pour l'éclairage
     */
    function push($model){
        $data = $this->request->data;
        $ecl[':id_rue'] = $data->id_rue;

        $sql = 'SELECT * FROM func_insert_eclaire(:id_rue)';

        $pre = $model->db->prepare($sql);
        $pre->execute($ecl);
        $res = $pre->fetchAll(PDO::FETCH_OBJ);

        if($res){//si la ligne a bien été inséré
            $this->Session->setFlash("Votre demande d'éclairage a bien été accepté");
        }else{//si la ligne a été refusé d'insertion
            $this->Session->setFlash("Les lumières de cette rue sont déjà allumé",'fail');
        }
    }


}