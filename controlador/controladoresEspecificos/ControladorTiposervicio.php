<?php
require_once 'ControladorGeneral.php';
require_once 'ControladorMaster.php';
class ControladorTiposervicio extends ControladorGeneral {
public function buscar() {
(string)$tabla = get_class($this);
$master = new ControladorMaster();
return $master->buscar($tabla);
}
public function eliminar($id) {
(string) $tabla = get_class($this);
$master = new ControladorMaster();
$master->eliminar($tabla, $id);
return ['eliminado'=>'eliminado'];
}
 public function buscarUsuarioXId($dato) {
(string)$tabla = get_class($this);
$master = new ControladorMaster();
return $master->buscarId($dato, $tabla);
}
 public function guardar($datosCampos) {
(string)$tabla = get_class($this);
$master = new ControladorMaster();
return $master->guardar($tabla,$datosCampos);
}
 public function ultimo() {
(string)$tabla = get_class($this);
$master = new ControladorMaster();
return $master->bucarUltimo($tabla);
}
public function modificar($datosCampos) {
(string)$tabla = get_class($this);
$master = new ControladorMaster();
return $master->modificar($tabla, $datosCampos);
}
}
