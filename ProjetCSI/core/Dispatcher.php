<?php
//récupère l'url et sait quoi en faire

class Dispatcher{

    var $request;

    function __construct(){
        $this->request = new Request();
        Router::parse($this->request->url, $this->request);

        $controller = $this->loadController();

        $action = $this->request->action;
        if($this->request->prefix){
            $action =$this->request->prefix.'_'.$action;
        }

        //avant d'appeler le controlleur en lui même, on vérifie qu'il existe bien pour éviter toutes erreurs
        if(!in_array($action,array_diff(get_class_methods($controller),get_class_methods('Controller')))){
            $this->error('Le controller '.$this->request->controller.' n\'a pas de méthode '.$action);
        }
        call_user_func_array(array($controller,$action), $this->request->params);
        $controller->render($action);
    }

    function loadController(){
        $name = ucfirst($this->request->controller).'Controller'; // on créer le nom du fichier de type NomController
        $file = ROOT.DS.'controller'.DS.$name.'.php';
        require $file; //on vérifie que ce fichier existe bien
        $controller = new $name($this->request);

        return  $controller;
    }


    //affichage/explication des erreurs
    function error($message){
        $controller = new Controller($this->request);
        $controller->e404($message);
    }

} 