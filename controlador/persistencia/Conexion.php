<?php

class Conexion {

    private $_conexion = null;

    public function __construct() {
        try {
            $entorno = parse_ini_file("../../config/.env", true);
            if (filter_input(INPUT_SERVER, "SERVER_NAME") == 'localhost') {
                $this->_conexion = new PDO("mysql:dbname=" . $entorno['DB_DB'] . ";host=" .
                        $entorno['DB_HOST'], $entorno['USER_DB'], $entorno['PASS_DB'],
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));
            } else {
                $this->_conexion = new PDO("mysql:dbname=" . $entorno['DB_DB'] . ";host=" .
                        $entorno['DB_HOST'], $entorno['USER_DB_PROD'], $entorno['PASS_DB_PROD'],
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));
            }
            $this->_conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            file_put_contents("log/dberror.log", "Date: " . date('M j Y - G:i:s') .
                    " ---- Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            die($e->getMessage());
        }
    }

    public function getConexion() {
        return $this->_conexion;
    }

    public function __destruct() {
        try {
            $this->_conexion = null; //Closes connection
        } catch (PDOException $e) {
            file_put_contents("log/dberror.log", "Fecha: " . date('M j Y - G:i:s') .
                    " ---- Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            die($e->getMessage());
        }
    }

}
