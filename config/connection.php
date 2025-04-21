<?php

    class Database{
        private $hostname = "localhost";
        private $database = "comter";
        private $username = "root";
        private $password = "admin1234$";
        private $charset = "utf8mb4";

        function conectar()
        {
            try{
            $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . ";
            charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false 
            ];
            $pdo = new PDO($conexion, $this->username, $this->password, $options);

            return $pdo;
        }catch(PDOException $e){
            echo 'Error de conexión: ' . $e->getMessage();
            exit;
        }
        }
        public function getDatabaseName() {
            return $this->database;
        }
    }
