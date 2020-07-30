<?php

require_once 'ControladorGeneral.php';
require_once 'SqlQuery.php';
require_once '../persistencia/ControladorPersistencia.php';
/*
 * Clase generada para servir de controlador maestro 
 */

/**
 * Description of ControladorMaster
 *
 * @author DIEGO
 */
class ControladorMaster {

    protected $refControladorPersistencia;

    function __construct() {
        $this->refControladorPersistencia = new ControladorPersistencia();
    }

    public function buscar($tabla) {
        $buscar = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienza la transacción
            $statement = $this->refControladorPersistencia->ejecutarSentencia(
                    $buscar->buscar($tabla)); //senencia armada desde la clase SqlQuery sirve para comenzar la busqueda
            $array = $statement->fetchAll(PDO::FETCH_ASSOC); //retorna un array asociativo para no duplicar datos
            $this->refControladorPersistencia->get_conexion()->commit(); //si todo salió bien hace el commit            
            return $array; //regreso el array para poder mostrar los datos en la vista... con Ajax... y dataTable de JavaScript
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        }
    }

    public function eliminar($tabla, $id) {
        $eliminar = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienzo la transacción
            $this->refControladorPersistencia->ejecutarSentencia(
                    $eliminar->eliminar($tabla, $id)); //Uso la funcion correspondiente de controlador pesistencia         
            $this->refControladorPersistencia->get_conexion()->commit(); //ejecuto la acción para eliminar de forma lógica a los ususario
        } catch (PDOException $excepcionPDO) {//excepcion para controlar los errores
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        return ["eliminado" => "eliminado"];
    }

    public function guardar($tabla, $datosCampos) {
        $guardar = new SqlQuery();
        $existe = $this->verificar($tabla, $datosCampos);
        if ($existe[0] == '0') {//solamente si el usuario no existe se comienza con la carga a la BD
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
            $respuesta = $this->getUsuario($id, $tabla); //busco el usuario
            return $respuesta; //regreso
        } else {
            return $id = ["incorrecto" => "incorrecto"]; //si hubo un error volvemos a vista y corregimos
        }
    }

    public function modificar($tabla, $datosCampos) {
        $guardar = new SqlQuery();
        $id = $datosCampos["id"];
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción 
            $arrayCabecera = $guardar->meta($tabla); //armo el array con la cabecera de los datos
            $sentencia = $guardar->armarSentenciaModificar($arrayCabecera, $tabla); //genero sentencia
            $array = $guardar->armarArray($arrayCabecera, $datosCampos); //Armo el array con los datos que vienen de la vista y la cabecera de la BD
            array_shift($array); //elimino primer elemento del array que es el id
            array_push($array, $id); //agrego el id al final del array para realizar la consulta
            $this->refControladorPersistencia->ejecutarSentencia($sentencia, $array); //genero la consulta a la BD            
            $this->refControladorPersistencia->get_conexion()->commit();  //si todo salió bien hace el commit            
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        $respuesta = $this->getUsuario($id, $tabla);
        return $respuesta;
    }

    public function modificarClave($tabla, $datosCampos) {
        $fecha = time() - (5 * 60 * 60); // le resto 5 horas a la fecha para que me dé la hora argentina
        $fechaActual = date('Y-m-d H:i:s', $fecha);
        $modificarUser = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
            $paramCambiarClave = ["clave_usuario" => sha1($datosCampos["clave_usuario"]), "fch_modificacion" => $fechaActual, "id_usuario" => $datosCampos["id_usuario"]];
            $this->refControladorPersistencia->ejecutarSentencia($modificarUser->senteciaModificarUsuarioClave($tabla),
                    $paramCambiarClave);
            $this->refControladorPersistencia->get_conexion()->commit(); //si todo salió bien hace el commit
            return $rtaCambio = ["cambio" => "ok"];
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        }
    }
    
    public function buscarId($dato, $tabla){
        $buscar = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();
            $usuarioConsulta = $this->refControladorPersistencia->ejecutarSentencia(
                    $buscar->buscarId($dato, $tabla));
            $arrayUsuario = $usuarioConsulta->fetchAll(PDO::FETCH_ASSOC); //utilizo el FETCH_ASSOC para que no repita los campos
            $this->refControladorPersistencia->get_conexion()->commit(); //realizo el commit para obtener los datos
            return $arrayUsuario; //regreso el array de usuario que necesito para mostrar los datos que han sido almacenados en la base de datos.
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        
    }
    
    public function bucarUltimo($tabla){
        $ultimo = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();
            $usuarioConsulta = $this->refControladorPersistencia->ejecutarSentencia($ultimo->buscarUltimo($tabla)); //en esta consulta busco cual es el ultimo usuario            
            $arrayUsuario = $usuarioConsulta->fetchAll(PDO::FETCH_ASSOC); //utilizo el FETCH_ASSOC para que no repita los campos
            $this->refControladorPersistencia->get_conexion()->commit(); //realizo el commit de los datos a la base de datos
            $idUsuario = ""; //creo una variable para poder enviar los datos al metodo correpondiente
            foreach ($arrayUsuario as $id) {//recorro el array que contiene los datos que necesito para buscarl el ultimo usuario
                foreach ($id as $clave => $value) {//recorro los datos dentro del array y obtengo el valor que necesito
                    $idUsuario = $value; //asigno el valor correspondiente a la variable creada anteriormente para tal caso
                }
            }
            //envio los datos al metodo que se va a encargar de ralizar la consulta a la base de 
            //datos para obtener el último usiario registrado y devolver los datos para mostrarlos por pantalla
            $usuarioId = $this->buscarUsuarioXId($idUsuario, $tabla); //lamo al metodo para obtener todos los datos del usuario que 
            //estoy buscando en este caso el último que se creo
            return $usuarioId; //regreso los datos de ese usuario a la llamada para enviarlos desde el ruteador a la vista
        } catch (PDOException $excepcionPDO) { //atrapo la excepcion por si algo salio mal que se realice el rollback           
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
    }

    public function getUsuario($id, $tabla) {
        $getUsuario = new SqlQuery();
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
            $usuario = $getUsuario->buscarId($id, $tabla);
            $statement = $this->refControladorPersistencia->ejecutarSentencia($usuario); //llamo a la funcion
            $user = $statement->fetchAll(PDO::FETCH_ASSOC);
            $this->refControladorPersistencia->get_conexion()->commit();  //si todo salió bien hace el commit            
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack();  //si hay algún error hace rollback
        }
        return $user;
    }

    public function getCampo($datosCampos) {
        $i = 0;
        foreach ($datosCampos as $key => $value) {
            if ($i == 1) {
                return $key;
            }
            $i++;
        }
    }

    public function verificar($tabla, $datosCampos) {
        $verifica = new SqlQuery();
        $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienza transaccion
        if ($tabla == "ControladorUsuario") {//uso para diferenciar si es usuario u otra clase
            $rtaVerifUser = $this->refControladorPersistencia->ejecutarSentencia(
                    $verifica->verificarExistenciaUsuario($tabla, $datosCampos["usuario"])); //verifico existencia de usuairo
        } else {

            $rtaVerifUser = $this->refControladorPersistencia->ejecutarSentencia(
                    $verifica->verificarExistencia($tabla, $datosCampos[$this->getCampo($datosCampos)])); //verifico si ya hay un usuario con ese nombre 
        }
        $existe = $rtaVerifUser->fetch(); //paso a un array
        $this->refControladorPersistencia->get_conexion()->commit(); //cierro
        return $existe;
    }

}
