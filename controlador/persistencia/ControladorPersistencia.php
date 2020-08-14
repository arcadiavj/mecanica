<?php
require_once 'Conexion.php';
require_once 'DBSentencias.php';

class ControladorPersistencia implements DBSentencias {
    private $_conexion = null;
    function get_conexion() {
        return $this->_conexion;
    }

    public function __construct() {
        $db = new Conexion();
        $this->_conexion = $db->getConexion();
    }
    public function ejecutarSentencia($query, $parametros = null) {
//       var_dump($query);
//        var_dump($parametros);
        $statement = $this->_conexion->prepare($query);
        if($parametros) {
            $index = 1;
            foreach ($parametros as $key => $parametro) {
                $statement->bindValue($index, $parametro);
                $index ++;
            }
        }
        $statement->execute();
        return $statement;
    }
    
}
