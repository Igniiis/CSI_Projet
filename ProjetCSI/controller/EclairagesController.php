<?php

class EclairagesController extends Controller{

    function ask($id = null){
        $this->loadModel('eclairage');
        $this->Eclairage = new Eclairage();
        $d['id'] = '';

        if($this->request->data){
            $this->push($this->Eclairage, $this->request->data);
            $id = $this->Eclairage->id;
        }
        
        $file = ROOT.DS.'model'.DS.'Rue'.'.php';
        require_once($file);
        if(!isset($this->Rue)){
            $this->Rue = new Rue();
        }
        $d['rues'] = $this->Rue->find(array());

        $this->set($d);
    }



    function admin_ask($id = null){
        $this->loadModel('eclairage');
        $this->Eclairage = new Eclairage();
        $d['id'] = '';

        if($this->request->data){
            $this->push($this->Eclairage, $this->request->data);
            $id = $this->Eclairage->id;
        }
        
        $file = ROOT.DS.'model'.DS.'Rue'.'.php';
        require_once($file);
        if(!isset($this->Rue)){
            $this->Rue = new Rue();
        }
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
    function push($model, $data){
        $ecl[':id_rue'] = $data->id_rue;

        $sql = 'SELECT * FROM func_insert_eclaire(:id_rue)';

        //print_r($sql.'<br>');
        //print_r($ecl);

        $pre = $model->db->prepare($sql);
        $pre->execute($ecl);
        $res = $pre->fetchAll(PDO::FETCH_OBJ);

        //print_r($res[0]->func_insert_eclaire);
        switch ($res[0]->func_insert_eclaire) {
            case 0:
                $this->Session->setFlash('Les lumières de cette rue sont déjà allumé','fail');
                break;
            case 1:
                //rajoute 15 minutes après l'éclairage actuel
                $this->Session->setFlash("Les lumières de cette rue sont allumé pour les 15 prochaines minutes");
                break;
            case 2:
                //allume la lumière directement
                $this->Session->setFlash("Nous avons rajouter 15 minutes d'éclairage dans cette rue");
                break;
            default:
                # code...
                break;
        }

    }


}