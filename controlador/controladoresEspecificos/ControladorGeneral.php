<?php
require_once '../persistencia/ControladorPersistencia.php';

abstract class ControladorGeneral {
    protected $refControladorPersistencia;
    function __construct() {
        $this->refControladorPersistencia = new ControladorPersistencia();
    }
    //public abstract function guardar($datosCampos);
    public abstract function guardar($datosCampos);
    public abstract function modificar($datosCampos);
    public abstract function eliminar($datosCampos);
    public abstract function buscar();
}
