<?php
    require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");
    include_once("gara.class.php");

    class Manifestazione{
        public $id;
        public $nome;
        public $luogo;
        public $data;
        public $id_finveneto;
        public $gare;

        function __construct ($id,$nome = null,$luogo = null,$data = null,$id_finveneto = null,$gare = null){
            $this->id = $id;
            $this->nome = $nome;
            $this->luogo = $luogo;
            $this->data = $data;
            $this->id_finveneto = $id_finveneto;
            $this->gare = $gare;
        }

        function getData(){
            $database = Database::getInstance();
            
            $res = $database->query("SELECT nome,luogo,data,id_finveneto FROM manifestazione WHERE id='".$this->id."' LIMIT 1");
            $row = $res->fetch_assoc();
            $this->nome = $row['nome'];
            $this->luogo = $row['luogo'];
            $this->data = $row['data'];
            $this->id_finveneto = $row['id_finveneto'];
            
            return $this;
        }

        function getGare(){
            $database = Database::getInstance();

            $res =  $database->query("SELECT id,metri,stile,categoria,conclusa FROM gara WHERE id_manifestazione = '$this->id'");
            while($row = $res->fetch_assoc()){
                $this->gare[] = new Gara($row['id'],$this,$row['metri'],$row['stile'],$row['categoria'],$row['conclusa']);
            }
            return $this;
        }

        function updateData(){
            $database = Database::getInstance();
            $database->query("UPDATE manifestazione SET nome = '$this->nome', luogo = '$this->luogo', data='$this->data', id_finveneto = '$this->id_finveneto' WHERE id='$this->id'");
        }

        //FUNZIONI DI OUTPUT

        function outputData(){
            return date("d/m/y",strtotime($this->data));
        }

        function outputInfo(){
            return $this->nome." ".$this->luogo;
        }

        //FUNZIONI STATICHE

        static function getManifestazioniFromDate($min_data = null,$max_data = null,$limit = 5){
            $database = Database::getInstance();
    
            $manifestazioni = array();
            if($min_data != null){
                $min_data_str = "data > '$min_data'";
                $where_str = $min_data_str;
            }
            if($max_data != null){
                $max_data_str = "data <= '$max_data'";
                $where_str = $max_data_str;
            }
            if(isset($min_data_str) && isset($max_data_str)){
                $where_str = "$min_data_str AND $max_data_str";
            }
            $query_manifestazioni = $database->query("SELECT id,nome,data,luogo FROM manifestazione WHERE $where_str ORDER BY data DESC LIMIT $limit");
            while($manif = $query_manifestazioni->fetch_assoc()){
                $manifestazioni[] = new Manifestazione($manif['id'],$manif['nome'],$manif['luogo'],$manif['data']);
            }
            return $manifestazioni;
        }

    }
