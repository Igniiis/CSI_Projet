<?php

class SignalementsController extends Controller{

    function index(){

        $this->loadModel('signalement');
        $this->Signalement = new Signalement();

        if($this->request->data){
            if($this->insertion_complète()){
                $this->redirect('accueil/succes');
            }
        }


        $this->loadModel('rue');
        $this->Rue = new Rue();
        
        $d['rues'] = $this->Rue->find(array());
        $this->set($d);
    }


    /**
     * ADMIN
     */

     
    function admin_view(){
        
        $this->loadModel('signalement');
        $this->Signalement = new Signalement();

        $d['signalements'] = $this->Signalement->find(array(
            'fields' =>'id_signalement,probleme,numero_maison_proche,intervalle_numero_debut,intervalle_numero_fin,description_probleme,niveau_urgence,date_signalement,compteur_signalement_total,compteur_signalement_anonyme,etat,nom_rue,date_resolution,date_modification',
            'joins' => 'INNER JOIN rue ON Signalement.id_rue=rue.id_rue '
        ));

        $this->set($d);
    }


    /**
     * Pour éditer un signalement précis
     */
    function admin_edit($id = null){
        $this->loadModel('signalement');
        $this->Signalement = new Signalement();
        $d['id'] = '';

        if($this->request->data){
            if($this->Signalement->save($this->request->data)=='insert'){
                $this->Session->setFlash("Le contenu a bien été créé à l'id {$id}");                
            }else{
                $this->Session->setFlash("Le contenu a bien été modifié (id:{$id})");
            }
            $id = $this->Signalement->id;
        }

        if($id){
            $this->request->data = $this->Signalement->findFirst(array(
                'fields' =>'id_signalement,probleme,numero_maison_proche,intervalle_numero_debut,intervalle_numero_fin,description_probleme,niveau_urgence,date_signalement,compteur_signalement_total,etat,id_rue',
                'conditions' => array('id_signalement'=>$id)
            ));
            $d['id'] = $id;
        }

        $file = ROOT.DS.'model'.DS.'Rue'.'.php';
        require_once($file);
        if(!isset($this->Rue)){
            $this->Rue = new Rue();
        }
        $d['rues'] = $this->Rue->find(array());


        $this->set($d);
    }


    /**
     * Pour supprimer un signalement précis
     */
    function admin_delete($id){
        
        $this->loadModel('signalement');
        //on appel pas la fonction car elle marche pas, jsp pk...
        $this->Signalement = new Signalement();
        
        //pour supprimer la ligne de la table de liaison
        $sql = 'DELETE FROM signalement_habitant WHERE '.$this->Signalement->primaryKey.' = '.$id;
        $this->Signalement->db->query($sql);
        $this->Signalement->delete($id);
        $this->Session->setFlash("Le contenu a bien été supprimé {$id}");
        $this->redirect('admin/signalements/view');
        die();
    }


    private function insertion_complète(){
        $data = $this->request->data;
        $sig = array();
        $coo = array();
        
        unset($data->drone);
        if(isset($data->checking)){ //si on a signalement ainsi que des coordonnées
            $coo[':nom_h'] = trim(strtolower($data->nom_habitant));
            $coo[':prenom_h'] = trim(strtolower($data->prenom_habitant));
            $coo[':num_adresse_h'] = trim(strtolower($data->num_adresse_habitant));
            $coo[':id_r'] = trim(strtolower($data->id_rue_habitant));
            $coo[':numero_p'] = trim(strtolower($data->numero_portable));
            $coo[':numero_f'] = trim(strtolower($data->numero_fixe));
            $coo[':mail_h'] = trim(strtolower($data->mail));
            
            unset($data->checking);
        }
        
        unset($data->nom_habitant);
        unset($data->prenom_habitant);
        unset($data->num_adresse_habitant);
        unset($data->id_rue_habitant);
        unset($data->numero_portable);
        unset($data->numero_fixe);
        unset($data->mail);
        
        //pour ce qui est du signalement
        foreach ($data as $k => $v) {
            $sig[":$k"] = $v;

            $s[] = $k;
            $fieldsSig[] = ':'.$k;
        }
        $sql = 'INSERT INTO Signalement ( '.implode(', ',$s).' ) VALUES ('.implode(', ',$fieldsSig).')';
        $pre = $this->Signalement->db->prepare($sql);
        $pre->execute($sig);

        if(!empty($coo)){//si on n'envoi pas qu'un signalement
            $coo[':id_sig'] = $this->Signalement->db->lastInsertId();
            $sql = 'CALL proc_insert_habitant_signalement(:id_sig, :nom_h, :prenom_h, :id_r, :num_adresse_h, :numero_p, :numero_f, :mail_h)';
        
            $pre = $this->Signalement->db->prepare($sql);
            $pre->execute($coo);
        }

        return true;
    }

}