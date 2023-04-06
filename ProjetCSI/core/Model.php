<?php
class Model {

    /**
     * variable static, qui sera donc unique à toutes les classe model
     * et qui permet l'unicité de la connexion à la bdd, même si plusieurs models sont créé
     */
    static $connections = array();

    public $primaryKey = 'id';
    public $id;

    public $table = false;
    public $db;

    public $conf = 'default'; //base de donnée à prendre, par defaut est set à 'default'

    public function __construct(){
        //connexion à la base
        $conf = Conf::$database[$this->conf];
        
        if(isset(Model::$connections[$this->conf])){
            $this->db = Model::$connections[$this->conf];
            $this->table = get_class($this);
            return TRUE;
        }

        try{
            $pdo = new PDO('pgsql:host='.$conf['host'].';port='.$conf['port'].';dbname='.$conf['database'], $conf['login'], $conf['password']);
            Model::$connections[$this->conf] = $pdo; 
            $this->db = Model::$connections[$this->conf];
        }catch(PDOException $e){
            if(Conf::$debug>1){
                die(print_r($e,true));
            }else{
                die('impossible de se connecter à la base de donnée');
            }   
        }

        //J'initialise quelques variables
        if($this->table === false){
            //au cas où le nom de la table n'est pas set manuellement dans le constructeur, on donne le mon de l'objet appelé
            $this->table = get_class($this);
        }
    }

    public function find($req){
        //les champs à récupérer
        $fields = '*';
        if(isset($req['fields'])){
            $fields = $req['fields'];
        }

        $sql = 'SELECT '. $fields .' FROM '.$this->table.' AS '.get_class($this).' ';

        //les différents Inner Join
        if(isset($req['joins'])){
            $sql .= $req['joins'];
        }

        //les différentes conditions WHERE
        if(isset($req['conditions'])){ //si il y a des conditions
            $sql .= 'WHERE ';
            if(!is_array($req['conditions'])){
                $sql .= $req['conditions'];
            }else{
                $cond = array();
                foreach($req['conditions'] as $k=>$v){
                    if(!is_numeric($v)){
                        $v = "'".pg_escape_string($v)."'";
                    }
                    $cond[] = "$k=$v";
                }
                $sql .= implode(' AND ', $cond);
            }
        }

        if(isset($req['conditions_plus'])){
            $sql .= $req['conditions_plus'];
        }

        if(isset($req['order by'])){
            $sql .= ' ORDER BY '.$req['order by'];
        }

        $pre = $this->db->prepare($sql);
        $pre->execute();
        return $pre->fetchAll(PDO::FETCH_OBJ);
    }


    /**
     * Retourne seulement le premier résultat du find
     */
    public function findFirst($req){
        return current($this->find($req));
    }



    /**
     * Execute une commande de suppression
     */
    public function delete($id){
        $sql = 'DELETE FROM '.$this->table.' WHERE '.$this->primaryKey.' = '.$id;
     
        $this->db->query($sql);
    }


    public function save($data){
        $key = $this->primaryKey;

        $fields = array();
        $d = array();
        $f = array();
        
        if(isset($data->$key) && $data->$key==''){
            unset($data->$key);
        }

        foreach ($data as $k => $v) {
            if($v==''){
                $v=null;
            }
            $d[":$k"] = $v;

            $f[] = $k;
            $fieldsInsert[] = ':'.$k;
            $fieldsUpdate[] = $k .'=:'.$k;
        }

        if(isset($data->$key) && !empty($data->$key)){
            $sql = 'UPDATE '.$this->table.' SET '.implode(', ',$fieldsUpdate).' WHERE '.$key.'=:'.$key;
            $this->id = $data->$key;
            $action = 'update';
        }else{
            if(isset($data->$key)) unset($data->$key);
            $sql = 'INSERT INTO '.$this->table.'( '.implode(', ',$f).' ) VALUES ('.implode(', ',$fieldsInsert).')';
            $action = 'insert';
        }

        $pre = $this->db->prepare($sql);
        $pre->execute($d);
        if($action=='insert'){
            $this->id = $this->db->lastInsertId();
        }
        return $action;
    }
}