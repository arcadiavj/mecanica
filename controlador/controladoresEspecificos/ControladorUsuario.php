<?php

require_once 'ControladorGeneral.php';
require_once 'ControladorMaster.php';

class ControladorUsuario extends ControladorGeneral {

    
    public function cambiarClave($datosCampos) {
        (string) $tabla = get_class($this);//obtengo el nombre de la clase para comunicarme a la base de datos
        $master = new ControladorMaster();//instancion contolador master
        return $master->modificarClave($tabla, $datosCampos);//llamo a la funcion necesaria para realizar la consulta
    }

    public function buscar() {//busca usando la clase SqlQuery
        (string) $tabla = get_class($this); //uso el nombre de la clase que debe coincidir con la BD         
        $master = new ControladorMaster();
        return $master->buscar($tabla);
    }

    public function eliminar($id) {//elimina usando SqlQuery clase
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        $master = new ControladorMaster();
        $master->eliminar($tabla, $id);
        return ["eliminado" => "eliminado"];
    }

    public function buscarUsuarioXId($dato) {//este método es el encargado de realiza la busqueda del último usuario insertado usando SqlQuery      
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        $master = new ControladorMaster();
        return $master->buscarId($dato, $tabla);
    }

    public function guardar($datosCampos) {//funcion guardar con SqlQuery implementado
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
        $datosCampos["pass"]= sha1($datosCampos["pass"]);
        $master = new ControladorMaster();
        return $master->guardar($tabla, $datosCampos);
    }

    public function ultimoUsuario() {//utiliza clase SqlQuery para automatizar consulta        
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para realizar la consulta en la BD
        $master = new ControladorMaster();
        return $master->bucarUltimo($tabla);
    }

    public function modificar($datosCampos) {//utiliza clase SqlQuery para automatizar consulta
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para poder realizar la consulta
        switch ($datosCampos["acceso"]) { //cambio los dato que vienen de la vista
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
        $master = new ControladorMaster();
        return $master->modificar($tabla, $datosCampos);
    }

    public function cerrarSesion($datos) {
        (string) $tabla = get_class($this);
        $master = new ControladorMaster();
        $usuario = $master->getUsuario($datos["usuario"], $tabla);
        return $usuario;
    }

    public function validarUsuarioClave($user, $pass) {
        $validar = new SqlQuery();
        (string) $tabla = get_class($this);
        try {
            $sentencia = $validar->checkUser($tabla);
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
            $statement = $this->refControladorPersistencia->ejecutarSentencia($sentencia, (array) $user); //
            $resultado = $statement->fetch();
            $this->refControladorPersistencia->get_conexion()->commit(); //si todo salió bien hace el commit*/            
            if (!$resultado) { //no existe usuario
                session_start();
                session_destroy();
                return $res = ["falla" => "user"];
            } else if ((strcasecmp($resultado['clave_usuario'], sha1($pass))) == 0) {
                if (strcasecmp($pass, "123") == 0) {// primer ingreso, debe cambiar la contraseña
                    //por ahora está igual, pero cambiar
                    session_start();
                    $_SESSION["usuario_usuario"] = $user;
                    $_SESSION["id_usuario"] = $resultado['id_usuario'];
                    $_SESSION["tipoAcceso_usuario"] = $resultado['tipoAcceso_usuario'];                 
                    return $res = ["usuario_usuario" => $user, "id_usuario" => $resultado['id_usuario'], "tipoAcceso_usuario" => $resultado['tipoAcceso_usuario'], "cambiarClave" => "cambiar"];
                } else {//ya la ha cambiado, ingreso correcto
                    session_start();
                    $_SESSION["usuario_usuario"] = $user;
                    $_SESSION["id_usuario"] = $resultado['id_usuario'];
                    $_SESSION["tipoAcceso_usuario"] = $resultado['tipoAcceso_usuario'];
                    $paramCons = ["id_usuario" => $_SESSION["id_usuario"]]; //parametros para acutalizar el id_consultorio del user logeado
                    return $res = ["usuario_usuario" => $user, "id_usuario" => $resultado['id_usuario'], "tipoAcceso_usuario" => $resultado['tipoAcceso_usuario']];
                }
            } else {
                session_start();
                session_destroy();
                return $res = ["falla" => "pass", "pass" => sha1($pass), "passbd" => $resultado['clave_usuario']];
            }
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        }
    }

}
