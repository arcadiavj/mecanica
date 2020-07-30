<?php

require_once 'ControladorGeneral.php';
//require_once 'SqlQuery.php';
require_once 'ControladorMaster.php';

class ControladorAreas extends ControladorGeneral {        

    

    public function buscar() {//busca usando la clase SqlQuery
        (string) $tabla = get_class($this); //uso el nombre de la clase que debe coincidir con la BD         
        $master = new ControladorMaster();
        return $master->buscar($tabla);
    }

    public function eliminar($id) {//elimina usando SqlQuery clase
        $eliminarUsuario = new SqlQuery(); //creo instancia de la clase encargada de armar sentencias
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienzo la transacción
            $this->refControladorPersistencia->ejecutarSentencia(
                    $eliminarUsuario->eliminar($tabla, $id)); //Uso la funcion correspondiente de controlador pesistencia         
            $this->refControladorPersistencia->get_conexion()->commit(); //ejecuto la acción para eliminar de forma lógica a los ususario
        } catch (PDOException $excepcionPDO) {//excepcion para controlar los errores
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        return ["eliminado"=>"eliminado"];
    }

    public function guardar($datosCampos) {//funcion guardar con SqlQuery implementado
        $guardar = new SqlQuery(); //instancio objeto de la clase sqlQuery
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para poder realizar la consulta
        switch ($datosCampos["acceso"]) {
            case "total":
                $datosCampos["acceso"] = 1;
                break;
            case "restringido":
                $datosCampos["acceso"] = 2;
                break;
            default:
                $datosCampos["acceso"] = 0;
                break;
        }
        $datosCampos["pass"] = sha1("123"); //agrego la contraseña en sha1 para que solicite el cambio cada vez que se cree un usuario
        $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienza transaccion
        $rtaVerifUser = $this->refControladorPersistencia->ejecutarSentencia(
            $guardar->verificarExistenciaUsuario($tabla, $datosCampos["usuario"])); //verifico si ya hay un usuario con ese nombre 
        $existeUser = $rtaVerifUser->fetch(); //paso a un array
        $this->refControladorPersistencia->get_conexion()->commit(); //cierro
        if ($existeUser[0] == '0') {//solamente si el usuario no existe se comienza con la carga a la BD
            try {
                $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
                $arrayCabecera = $guardar->meta($tabla); //armo la cabecera del array con los datos de la tabla de BD
                $sentencia = $guardar->armarSentencia($arrayCabecera, $tabla); //armo la sentencia
                $array = $guardar->armarArray($arrayCabecera, $datosCampos); //armo el array con los datos de la vista y los datos que obtuve de la BD 
                array_shift($array); //remuevo el primer elemento id si es nuevo se genera automaticamente en la BD
                $this->refControladorPersistencia->ejecutarSentencia($sentencia, $array); //genero la consulta
                $this->refControladorPersistencia->get_conexion()->commit();
                $this->refControladorPersistencia->get_conexion()->beginTransaction();
                $ultimo = $guardar->buscarUltimo($tabla);
                $idUser = $this->refControladorPersistencia->ejecutarSentencia($ultimo); //busco el ultimo usuario para mostrarlo en la vista                
                $id = $idUser->fetchColumn(); //array 
                $this->refControladorPersistencia->get_conexion()->commit();  //si todo salió bien hace el commit
            } catch (PDOException $excepcionPDO) {
                echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
                $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
            }
            $respuesta = $this->getUsuario($id); //busco el usuario
            return $respuesta; //regreso
        } else {
            return $id = ["incorrecto" => "incorrecto"]; //si hubo un error volvemos a vista y corregimos
        }
    }

    public function modificar($datosCampos) {//utiliza clase SqlQuery para automatizar consulta
        $guardar = new SqlQuery(); //instancio objeto de la clase sqlQuery
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para poder realizar la consulta
        $id = $datosCampos["id"];
        switch ($datosCampos["acceso"]) //cambio los dato que vienen de la vista
        {
            case "total":
                $datosCampos["acceso"] = 1;
                break;
            case "restringido":
                $datosCampos["acceso"] = 2;
                break;
            default:
                $datosCampos["acceso"] = 0;
                break;
        }
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción 
            $arrayCabecera = $guardar->meta($tabla);//armo el array con la cabecera de los datos
            $sentencia = $guardar->armarSentenciaModificar($arrayCabecera, $tabla);//genero sentencia
            $array = $guardar->armarArray($arrayCabecera, $datosCampos);//Armo el array con los datos que vienen de la vista y la cabecera de la BD
            array_shift($array);//elimino primer elemento del array que es el id
            array_push($array, $id);//agrego el id al final del array para realizar la consulta
            $this->refControladorPersistencia->ejecutarSentencia($sentencia, $array);//genero la consulta a la BD            
            $this->refControladorPersistencia->get_conexion()->commit();  //si todo salió bien hace el commit            
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        $respuesta = $this->getUsuario($id);
        return $respuesta;
    }

    

}