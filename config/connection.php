<?php

    class Database{
        private $hostname = "31.170.160.103";
        private $database = "u284778729_comter";
        private $username = "u284778729_comteruser";
        private $password = "I^AJo2moO/7p";
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
