<?php
/**
 * Created by PhpStorm.
 * User: itorres
 * Date: 5/15/18
 * Time: 9:01 PM
 */

class db{

    // specify your own database credentials
    private $host = "127.0.0.1";
    private $db_name = "DESARROLLO";
    private $username = "root";
    private $password = "P4d30984";
    public $conn;

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";port=3306", $this->username, $this->password);
            $this->conn->exec("set names utf8");

        }catch(PDOException $exception){
            echo "Connection errors: " . $exception->getMessage();
        }

        return $this->conn;
    }




}