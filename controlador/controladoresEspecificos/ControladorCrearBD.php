<?php
require_once '../persistencia/ControladorPersistencia.php';
require_once 'SqlQuery.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControladorCrearBD
 *
 * @author DIEGO
 */
class ControladorCrearBD {
    
    protected $refControladorPersistencia; //controlador persistencia utilizado para crear la conexion a la BD

    function __construct() {
        $this->refControladorPersistencia = new ControladorPersistencia();
    }
    public function createDB($datosCampos){
        $crear = new SqlQuery();
        var_dump($datosCampos);
        $consulta = $crear->crearBase($datosCampos);
        var_dump($consulta);
        return $consulta;
        
        
    }
}
