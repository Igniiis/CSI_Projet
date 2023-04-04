<?php

class Controller{
 
    public $request;
    private $vars = array(); //contient toutes les variables que je veux faire passer à la vue

    public $layout = 'default'; //le nom du fichier layout a utilisé qui normalisera l'html statique pour toutes les pages du site

    private $rendered = false;

    function __construct($request){ 

        $this->Session = new Session();
        $this->Form = new Form($this);

        if($request){
            $this->request = $request;
        }
        require ROOT.DS.'config'.DS.'hook.php';
    }

    public function render($view){
        if($this->rendered){
            return false;
        }
        extract($this->vars);

        if(strpos($view,'/')===0){  //si la position du / dans la chaine view est 0
            $view = ROOT.DS.'view'.DS.$view.'.php';
        }else{
            $view = ROOT.DS.'view'.DS.$this->request->controller.DS.$view.'.php';
        }
        
        ob_start();
        require($view);
        $content_for_layout = ob_get_clean();
        require ROOT.DS.'view'.DS.'layout'.DS.$this->layout.'.php';
        $this->rendered = true;
    }


    /**
     * fonction qui permet d'envoyer les variables à la vue
     * si il n'y a qu'un seul paramètre alors ca doit être un tableau qui contient toutes les variables et leurs noms en lui
     * 
     */
    public function set($key,$value=null){
        if(is_array($key)){
            $this->vars += $key;
        }else{
            $this->vars[$key] = $value;
        }
    }

    function loadModel($name){
        $file = ROOT.DS.'model'.DS.$name.'.php';
        require_once($file);
        if(!isset($this->$name)){
            $this->$name = new $name();
        }
    }

    /**
     * Permet de gérer les erreurs 404
     */
    function e404($message){
        //explique au navigateur que c'est une mauvaise page
        header("HTTP/1.0 404 Not Found");

        $this->set('message',$message);
        $this->render('/errors/404');
    }


    /**
     * Permet d'appeler un controller depuis une vue
     */
    function request($controller,$action){
        $controller .= 'Controller';
        require_once ROOT.DS.'controller'.DS.$controller.php;
        $c = new $controller();
        return $c->$action();
    }


    function redirect($url,$code=null){
        if($code == 301){
            header("HTTP/1.1 301 Moved Permanently");
        }
        header("Location: ".BASE_URL.'/'.$url);
    }
}

?>