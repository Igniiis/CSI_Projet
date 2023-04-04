<?php

class Router{
    
    static $prefixes = array();

    public static function prefix($url, $prefix){
        self::$prefixes[$url] = $prefix;
    }

    /**
     * Permet de parser une url
     * 
     * @param $url URL à parser
     * @param $request la requête qui va être remplie
     * @return true si tout c'est bien passé
     */
    static function parse($url, $request){
        $url = trim($url,'/');
        $params = explode('/',$url);
        if(in_array($params[0],array_keys(self::$prefixes))){
            $request->prefix = self::$prefixes[$params[0]];
            array_shift($params);
        }


        $request->controller = $params[0];
        $request->action = isset($params[1]) ? $params[1] : 'index'; //si param[1] est set, alors on retourne param[1] sinon on retourne 'index'
        $request->params = array_slice($params,2);
        return true;
    }
}

?>