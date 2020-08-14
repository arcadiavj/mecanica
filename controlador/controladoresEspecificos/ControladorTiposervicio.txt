<?php

require_once 'ControladorGeneral.php';
//require_once 'SqlQuery.php';
require_once 'ControladorMaster.php';

class ControladorTiposervicio extends ControladorGeneral {        
    
    public function buscar() {//busca usando la clase SqlQuery
        (string) $tabla = get_class($this); //uso el nombre de la clase que debe coincidir con la BD         
        $master = new ControladorMaster();
        return $master->buscar($tabla);        
    }

   
    public function eliminar($id) {//elimina usando SqlQuery clase
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        $master = new ControladorMaster();
        $master->eliminar($tabla, $id);       
        return ["eliminado"=>"eliminado"];
    }

    
    public function buscarUsuarioXId($dato) {//este método es el encargado de realiza la busqueda del último usuario insertado usando SqlQuery      
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        $master = new ControladorMaster();
        return $master->buscarId($dato, $tabla);
    }

    public function guardar($datosCampos) {//funcion guardar con SqlQuery implementado
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para poder realizar la consulta
        $master = new ControladorMaster();
        return $master->guardar($tabla,$datosCampos);         
    }

    public function ultimo() {//utiliza clase SqlQuery para automatizar consulta        
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para realizar la consulta en la BD
        $master = new ControladorMaster();
        return $master->bucarUltimo($tabla);
    }

    public function modificar($datosCampos) {//utiliza clase SqlQuery para automatizar consulta
        $guardar = new SqlQuery(); //instancio objeto de la clase sqlQuery
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para poder realizar la consulta
        $master = new ControladorMaster();
        return $master->modificar($tabla, $datosCampos);
    }
}