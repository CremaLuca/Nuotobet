<?php
class Database
{
   var $conn = NULL;
   var $host = "localhost";
   var $username = "nuotobet";
   var $password = "password";
   var $db_name = "my_nuotobet";

   public static $instance;

   public static function getInstance(){
      if(self::$instance == null){
         $database = new Database();
         self::$instance = new $database;
      }
      return self::$instance;
   }

   function connect()
   {
      if (!$this->conn)
         $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
      if ($this->conn->connect_errno) {
         echo "Failed to connect to MySQL: " . $this->conn->connect_error;
      }
   } // db_connect

   function query($query){
      if(!$this->conn)
         $this->connect();
      if($result = $this->conn->query($query)){
         return $result;
      }else{
         echo "MySQLi query error: ".$this->conn->error;
      }
   }

   function close(){
      $this->conn->close();
   }

   function sanitize($var)
   {
      $return = mysqli_real_escape_string($this->conn, $var);
      return $return;
   }
}
