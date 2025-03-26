<?php

class Database{

private $host="localhost";
private $username="root";
private $password="";
private $db_name="student_db";

protected $conn;

public function __construct(){
   try{
    $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
    $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    echo "Database connected successfully!";


   }  catch (PDOException $e) {
    echo "connection failed ".$e->getMessage();
}
return $this->conn; 
 

}


}










?>