<?php

class AgentsController extends Controller{

    function login(){
        if($this->request->data){
            $data = $this->request->data;
            $this->loadModel('Agent');
            $this->Agent = new Agent();
            $agent = $this->Agent->findFirst(array(
                'conditions' => array('id_agent' => $data->Login, 'mdp_agent' => $data->Password)
            ));

            //si c'est bien un agent de la bdd
            if(!empty($agent)){
                $this->Session->write('Agent',$agent);                
            }

            print_r($agent);
            $this->request->data->password = '';
        }

        //si information de session, on redirige vers les admins
        if($this->Session->isLogged()){
            $this->redirect('admin/accueil');        
        }

    }

    function admin_logout(){
        unset($_SESSION['Agent']);
        $this->Session->setFlash('Vous êtes déconnecté');

        $this->redirect('accueil');
    }


}