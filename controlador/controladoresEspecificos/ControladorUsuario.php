<?php

require_once 'ControladorGeneral.php';
require_once 'SqlQuery.php';

class ControladorUsuario extends ControladorGeneral {        

    public function cambiarClave($datosCampos) {
        $fecha = time() - (5 * 60 * 60); // le resto 5 horas a la fecha para que me dé la hora argentina
        $fechaActual = date('Y-m-d H:i:s', $fecha);
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
            $paramCambiarClave = ["clave_usuario" => sha1($datosCampos["clave_usuario"]), "fch_modificacion" => $fechaActual, "id_usuario" => $datosCampos["id_usuario"]];
            $this->refControladorPersistencia->ejecutarSentencia(DBSentencias::MODIFICAR_USUARIO_CLAVE, $paramCambiarClave);
            $ultimoUsuario = $this->refControladorPersistencia->ejecutarSentencia(DBSentencias::BUSCAR_ULTIMO_USUARIO);
            $idUltimoUser = $ultimoUsuario->fetchColumn();
            $this->refControladorPersistencia->get_conexion()->commit(); //si todo salió bien hace el commit
            //return $this->getUsuario($idUltimoUser);
            $rtaCambio = ["cambio" => "ok"];
            return $rtaCambio;
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        }
    }

    public function buscar() {//busca usando la clase SqlQuery
        $buscarUsuario = new SqlQuery(); //instancio la clase
        (string) $tabla = get_class($this); //uso el nombre de la clase que debe coincidir con la BD         
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction(); //comienza la transacción
            $statement = $this->refControladorPersistencia->ejecutarSentencia(
                $buscarUsuario->buscar($tabla)); //senencia armada desde la clase SqlQuery sirve para comenzar la busqueda
            $arrayUsuario = $statement->fetchAll(PDO::FETCH_ASSOC); //retorna un array asociativo para no duplicar datos
            $this->refControladorPersistencia->get_conexion()->commit(); //si todo salió bien hace el commit
            return $arrayUsuario; //regreso el array para poder mostrar los datos en la vista... con Ajax... y dataTable de JavaScript
        } catch (PDOException $excepcionPDO) {
            echo "<br>Error PDO: " . $excepcionPDO->getTraceAsString() . '<br>';
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            $this->refControladorPersistencia->get_conexion()->rollBack(); //si salio mal hace un rollback
        }
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

    public function buscarUsuarioXId($datos) {//este método es el encargado de realiza la busqueda del último usuario insertado usando SqlQuery      
        $buscarUsuarioId = new SqlQuery(); //creo instancia de la clase encargada de armar sentencias
        (string) $tabla = get_class($this); //adquiero el nombre de la clase para usar en la tabla
        try {
            $this->refControladorPersistencia->get_conexion()->beginTransaction();
            $usuarioConsulta = $this->refControladorPersistencia->ejecutarSentencia(
                $buscarUsuarioId->buscarId($datos, $tabla));
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
        getenv('DDBB_USER');
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

    public function ultimoUsuario() {//utiliza clase SqlQuery para automatizar consulta
        $ultimo = new SqlQuery(); //instancio objeto la case en cuestion
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para realizar la consulta en la BD
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
            $usuarioId = $this->buscarUsuarioXId($idUsuario); //lamo al metodo para obtener todos los datos del usuario que 
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

    public function cerrarSesion($datos) {
            $usuario = $this->getUsuario($datos["usuario"]);
            return $usuario;
    }

    public function validarUsuarioClave($user, $pass) {
        $validar = new SqlQuery();
        (string)$tabla= get_class($this);
        try {
            $sentencia = $validar->checkUser($tabla);
            $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
            $statement = $this->refControladorPersistencia->ejecutarSentencia($sentencia,(array)$user); //
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
                    //$_SESSION["id_consultorio"] = $cons;
                    //$paramCons = ["id_consultorio" => $cons, "id_usuario" => $_SESSION["id_usuario"]];
//                    $this->refControladorPersistencia->get_conexion()->beginTransaction();  //comienza la transacción
//                    $this->refControladorPersistencia->ejecutarSentencia(DBSentencias::ACTUALIZAR_CONSULTORIO_DE_USER, $paramCons);
//                    $rtaDescCons = $this->refControladorPersistencia->ejecutarSentencia(DBSentencias::TRAER_DESCRIPCION_CONSULTORIO, array($cons));
//                    $descConsultorio = $rtaDescCons->fetchColumn();
//                    $this->refControladorPersistencia->get_conexion()->commit();
//                    $_SESSION["descripcion_consultorio"] = $descConsultorio;
//                    var_dump($resultado);
                    return $res = ["usuario_usuario" => $user, "id_usuario" => $resultado['id_usuario'], "tipoAcceso_usuario" => $resultado['tipoAcceso_usuario'], "cambiarClave" => "cambiar"];
                } else {//ya la ha cambiado, ingreso correcto
                    session_start();
                    $_SESSION["usuario_usuario"] = $user;
                    $_SESSION["id_usuario"] = $resultado['id_usuario'];
                    $_SESSION["tipoAcceso_usuario"] = $resultado['tipoAcceso_usuario'];
                    //$_SESSION["id_consultorio"]=$cons;
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

    public function getUsuario($id) {//funcion con clase SqlQuery
        $getUsuario = new SqlQuery(); //instancion objeto de la clase para realizar consulta
        (string) $tabla = get_class($this); //obtengo el nombre de la clase para hacer la consulta
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

}