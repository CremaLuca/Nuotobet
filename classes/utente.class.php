<?php
    require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");

    class Utente{

        public $id;
        public $username;
        public $email;
        public $crediti;
        public $is_admin;

        function __construct($id,$username = null,$email = null,$crediti = null,$is_admin = null){
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->crediti = $crediti;
            $this->is_admin = $is_admin;
        }

        function getData(){
            $database = Database::getInstance();

            $res = $database->query("SELECT username,email,crediti,is_admin FROM utente WHERE id='$this->id' LIMIT 1");
            $row = $res->fetch_assoc();
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->crediti = $row['crediti'];
            $this->is_admin = $row['is_admin'];
        }

        function updateData(){
            $database = Database::getInstance();
            $database->query("UPDATE utente SET email='$this->email', crediti='$this->crediti' WHERE id='$this->id'");
        }
    }
?>