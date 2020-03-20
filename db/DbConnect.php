<?php

    class DbConnect{
        private $host = "0.0.0.0"; // 0.0.0.0 si Ratchet, database-werewolf si Docker
        private $dbName = "websocket"; 
        private $user = "root"; 
        private $pass = "password"; 

        public function connect(){
            try {
                $conn = new PDO("mysql:host=". $this->host .";port=3306;dbname=". $this->dbName, $this->user, $this->pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                return $conn; 
            } catch(PDOException $e) {
                echo 'Database error: ' . $e->getMessage(); 
            }
        }
    }
