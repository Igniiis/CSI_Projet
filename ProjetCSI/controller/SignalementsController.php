<?php

class SignalementsController extends Controller{

    function index(){

        $this->loadModel('signalement');
        $this->Signalement = new Signalement();

        if($this->request->data){
            if($this->insertion_complète()){
                $this->redirect('accueil');
            }
            $this->Session->setFlash("Signalement envoyé.");
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
     * ADMIN
     */

     
    function admin_view(){
        
        $this->loadModel('signalement');
        $this->Signalement = new Signalement();

        $d['signalements'] = $this->Signalement->find(array(
            'fields' =>'id_signalement,probleme,numero_maison_proche,intervalle_numero_debut,intervalle_numero_fin,description_probleme,niveau_urgence,date_signalement,compteur_signalement_total,etat,nom_rue',
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
            $this->Signalement->save($this->request->data);
            $this->Session->setFlash("Le contenu a bien été modifié {$id}");
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
            $c[] = 'nom_habitant';
            $c[] = 'prenom_habitant';
            $c[] = 'num_adresse_habitant';
            $c[] = 'id_rue';
            $c[] = 'numero_portable';
            $c[] = 'numero_fixe';
            $c[] = 'mail';

            $fieldsCoo[] = 'nom_habitant';
            $fieldsCoo[] = 'prenom_habitant';
            $fieldsCoo[] = 'num_adresse_habitant';
            $fieldsCoo[] = 'id_rue';
            $fieldsCoo[] = 'numero_portable';
            $fieldsCoo[] = 'numero_fixe';
            $fieldsCoo[] = 'mail';


            $coo[':nom_habitant'] = $data->nom_habitant;
            $coo[':prenom_habitant'] = $data->prenom_habitant;
            $coo[':num_adresse_habitant'] = $data->num_adresse_habitant;
            $coo[':id_rue'] = $data->id_rue_habitant;
            $coo[':numero_portable'] = $data->numero_portable;
            $coo[':numero_fixe'] = $data->numero_fixe;
            $coo[':mail'] = $data->mail;
            
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
            $idS = $this->Signalement->db->lastInsertId();

            $sql = 'INSERT INTO Habitant ( '.implode(', ',$c).' ) VALUES ('.implode(', ', $fieldsCoo).')';
            $pre = $this->Signalement->db->prepare($sql);
            $pre->execute($coo);
            $idC = $this->Signalement->db->lastInsertId();

            //A VERIF
            $sql = 'INSERT INTO Signalement_habitant VALUES (id_signalement,id_habitant) VALUES (:id_s,:id_c)';
            $pre = $this->Signalement->db->prepare($sql);
            $pre->execute(array(
                ':id_s' => $idS,
                ':id_c' => $idC
            ));
        }

        return true;
    }

}