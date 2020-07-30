<?php

require_once '../persistencia/ControladorPersistencia.php'; //utilizo para la conexion

/**
 * Esta clase es generada para manejar los datos y sentencias que vienen de los controladores
 * la idea es no tener que escribir tanto códido en lo q a sentencias sql se refiere... me
 * recomiendo no dejar de utilizar del DBSentencias... pero solo hacerlo en el caso de las consultas cruzadas
 * cuando se trate de agregar en una sola tabla, el metodo a emplear es este.... sin ninguna duda
 * @author Diego
 */
class SqlQuery {

    protected $refControladorPersistencia; //controlador persistencia utilizado para crear la conexion a la BD

    function __construct() {
        $this->refControladorPersistencia = new ControladorPersistencia();
    }

    public function crearBase($datosCampos){//cre< una base de datos
        $sentencia = "CREATE DATABASE IF NOT EXISTS ".$datosCampos["nombreDB"]."; USE ".$datosCampos["nombreDB"].";" ;//senetcia sql que crea y usa la base de datos
        $sentencia .= $this->crearTabla($datosCampos);//llama a la funcion de crear tablas
        return $sentencia;//regresa la sentencia al controlador     
    }
    
    public function crearTabla($datosCampos) {//funcion que se va a encargar de crear las tablas en la base de datos seleccionada
        for ($index = 0; $index < count($datosCampos); $index++) {//for encargado de recorrer el datoscampo para crear la tabla en la base de datos 
            $sentencia = " CREATE TABLE IF NOT EXISTS `" . $datosCampos["nombre"] . "`(`" .
                    "id_" . $datosCampos["nombre"] . "` INT NOT NULL AUTO_INCREMENT, `" .
                    $datosCampos["nombre"] . "` " . $datosCampos["tipo"] . "(" . $datosCampos["caracteres"] . ") NOT NULL,";
            $sentencia .= "`fch_creacion` DATETIME NOT NULL, `fch_modificacion` "
                    . "DATETIME NOT NULL,"
                    . " `fch_baja` DATETIME NOT NULL," .
                    "PRIMARY KEY" . "(id_" . $datosCampos["nombre"] . ")" .
                    ") ENGINE=INNODB AUTO_INCREMENT=55 "
                    . "DEFAULT CHARSET=latin1;";
        }
        return $sentencia;//regresa la sentencia a la funcion crear BD
    }

    public function meta($tabla) {//funcion meta(), se utiliza para obtener los datos de la tabla en cuestion que luego serán mis variables en las sentencias... y también mis claves primarias
        $array = array(); //declaro array donde voy a armar los key a ser utilizados por el resto de los metodos 
        $this->refControladorPersistencia->get_conexion()->beginTransaction(); //abro la conexion para leer la BD
        $consulta = "DESCRIBE"; //con la consulta DESCRIBE $tabla´(éste es el nombre del controlador que obvio coincide con el de la tabla) obtengo la metadata de la BD
        $strTabla = strtolower(substr($tabla, 11)); //paso el nombre del controlador a minusculas para que MySQL no desconozca la tabla... siempre tengo cuidado de generar los nombres de la base de datos con minusculas
        $consulta .= ' ' . $strTabla; //concateno la consulta con el nombre de la tabla
        $variable = $this->refControladorPersistencia->ejecutarSentencia($consulta); //realizo la consulta en la BD
        $var = $variable->fetchAll(PDO::FETCH_ASSOC); //obtengo los valores
        $this->refControladorPersistencia->get_conexion()->commit();
        foreach ($var as $valores) {//hago un for para recorrer los valores que me devuelve la tabla
            foreach ($valores as $clave => $valor) {//este for es el que se encarga de llenar el arreglo con los Keys correpondiente que obtuve de la BD
                if ($clave == "Field") {//Field = Campo :) no lo voy a aclarar
                    $array[$valor] = "campo"; //lleno el array y completo los valores con un string cualquiera para saber q estoy trabajando
                }
            }
        }
        return $array; //regreso el array
    }

    public function armarSentencia($arrayCabecera, $tabla) {//ésta es la funcion encargada de generar la sentencia agregar en la base de datos
        $i = 1; //contador inicializado en 1 
        $strTabla = strtolower(substr($tabla, 11));
        $sentencia = "INSERT INTO " . $strTabla . " ("; //sentencia insert paso como dato el nombre de la tabla
        $llaveStr = ""; //inicializada en vacio, para poder cargarlo los campos de las base de dato a la sentencia
        array_shift($arrayCabecera); //elimino el primer componente del array... ya que si es insertar el id tiene q ser automatico
        $long = sizeof($arrayCabecera); //determino el tamaño del array para usarlo en el if de los foreach
        foreach ($arrayCabecera as $llave => $value) {// recorro el array ;)
            $llaveStr = " " . $llave; //ingreso los nombres de los campos de la BD en la sentencia
            if ($long > $i) {//si todavia hay metadata con nombres de campo sigo el recorrido del array
                $sentencia .= $llaveStr . ","; //agrego una coma despues de cada nuevo campo ingresado en la sentencia
            } else {//sino =)
                $sentencia .= $llaveStr . ')'; //finalizo los campos en cerrando con un parentesis
            }
            $i++; //autoincremento de la variable
        }
        if ($i != 1) {
            $i = 1;
        } //vuelvo a inicializar la variable en 1 para su posterior uso en el próximo foreach
        $sentencia .= ' VALUES ('; //concateno la sentencia con la cantidad de campos que se requieran para las incognitas
        foreach ($arrayCabecera as $llave => $value) {//vuelvo a recorrer el array... ahora poque no lo hago en el mismo array de arriba con dos variables distintas y después las concateno
            if ($long > $i) {//si no es el último campo...
                $sentencia .= '?, '; //cargo con una incognita mientras no se el último... se repite
            } else {//sino ;,
                $sentencia .= '? )'; //finalizo la sentencia
            }
            $i++; //incremento
        }
        return $sentencia; //regreso la sentencia
    }

    public function armarArray($arrayCabecera, $arrayDatos) {//ésta es para armar el array al momento de realizar la inserción en la BD
        $paramArray = array(); //array q se encarga de devolver los datos al controlador solicitante
        $limpArray = array(); //en este solo quito los datos que no representan nada en esta acción 
        $i = 0; //contador para los foreach de datos
        $j = 0; //contador para las fechas
        $id = array_values($arrayDatos)[0];
        foreach ($arrayDatos as $llave => $value) {//recorro los datos del array de datos
            if ($llave != "accion") {//mientras no lleguemos a la llave acción sequimos incorporando al array los datos
                $limpArray[$i] = $value; //todos los valores .... con su respectivo indice
            } else {//sino $)
                $i = 0; //reinicio el contador ... llegué al final
                break; //termino el método
            }
            $i++; //autoincremento la variable $i
        }
        foreach ($arrayCabecera as $llave => $value) {//utilizo este foreach para cargar los datos en un array con sus respectivas llaves.... tal cual esta en la BD
            $fch = explode("_", $llave); //verifico si es un campo fecha
            if ($fch[0] == "fch") {//si es un campo fecha ingreso 
                if ($j == 0 && $id == '0') {
                    $paramArray[$llave] = $this->fechaArray($id, $j, NULL); //llamo a la función correspondiente para que se encargue de llenar el array
                } else if ($j == 0) {
                    $paramArray[$llave] = $this->fechaArray($id, $j, $limpArray[$i]);
                } else {
                    $paramArray[$llave] = $this->fechaArray($id, $j, NULL);
                }
                $j++;
            } else {//sino *)
                $paramArray[$llave] = $limpArray[$i]; //cargo los datos del array con los datos que limpié en el foreach anterior...
            }
            $i++; //auto incremento
        }
        return $paramArray; //regreso el array que se encuentra armado y listo para ser insertado en la BD
    }

    public function buscarUltimo($tabla) {//Esta función busca el máximo ID cargado el la tabla correspondiente para reguresar el dato
        $strTabla = strtolower(substr($tabla, 11));
        $sentencia = "SELECT MAX(id_" . $strTabla . ") FROM " . $strTabla; //es solo para no repetir la sentencia un montón de veces en el BDSentencias
        return $sentencia; //obvio.... regreso la sentencia
    }

    public function buscar($tabla,$id = NULL, $campo=NULL) {//tuve que generar esta función para no cambiar la de la lógica que usamos en el controlador...
        $strTabla = strtolower(substr($tabla, 11)); //al obtener de la clase el nombre de la clase de digo que quiero que parta la palabra controlador y me haga la consulta con el nombre del formulario
        $sentencia = "";//string para guardar la sentencia q voy a devolver al controlador
        $contador = $this->verificarJoin($tabla);//verifico si es join o no
        if ($contador > 1 & $strTabla != "usuario") {//ingreso solo si el contador es mayor a 1 es decir si hay mas de un campo id en la base de datos
            $sentencia = $this->buscarInnerJoin($tabla,$id,$campo);//llamo a la funcion JOIN
        } else {            
            $sentencia = "SELECT * FROM " . $strTabla . " WHERE fch_baja = '0000-00-00 00:00:00'"; //inserto el nombre del formulario para relizar la consulta desde el controlador        
        }
        return $sentencia; //regreso la sentencia para ser usada..
    }
    
    public function buscarSuma($tabla,$campoSuma,$campoGrupo) {
        $strTabla = strtolower(substr($tabla, 11)); //al obtener de la clase el nombre de la clase de digo que quiero que parta la palabra controlador y me haga la consulta con el nombre del formulario
        $sentencia = "SELECT *, SUM(".$campoSuma.") AS 'dato' FROM ".$strTabla." GROUP BY ".$campoGrupo; //string para guardar la sentencia q voy a devolver al controlador
        return $sentencia;
    }

    public function checkUser($tabla) {
        $strTabla = substr($tabla, 11);
        $sentencia = "SELECT * FROM " . $strTabla . " WHERE " . $strTabla . "_" . $strTabla . " = ?";
        return $sentencia;
    }

    public function fecha() {//utilizado para cargas las fechas actuales
        $fecha = time(); //coloca la fecha actual
        $fechaFormato = date('Y-m-d H:i:s', $fecha); //le doy el formato que necesito.... y sigo
        return $fechaFormato; //regreso la fecha
    }

    public function eliminar($tabla, $id) {//está demás decir para que sirve esta función ... todavía basica... pero funcional
        $fecha = $this->fecha(); // desde aquí llamo a la función fecha... que sirve para ingresar la fecha con el formato correspondiente en la BD
        $strTabla = substr($tabla, 11); //al obtener de la clase el nombre de la clase de digo que quiero que parta la palabra controlador y me haga la consulta con el nombre del formulario
        $consulta = "UPDATE " . $strTabla . " SET fch_baja = '" . $fecha . "' WHERE id_" . $strTabla . " =" . $id; // se genera la sentencia..
        return $consulta; //regreso la consula... '(
    }

    public function fechaArray($id, $j, $valor) {//utilizado para colocar la fecha correspondiente en el array que 
        $fechaFormato = "";
        if ($id == '0' && $j == 0) {//mi contador está en 0 por tanto es una fecha de AGREGAR...  
            $fechaFormato = $this->fecha(); //llamo a la funcion correspondiente encargada de poner la fecha actual para enviar en la llave correspodinte a la BD         
        } else if ($id != '0' && $j == 1) {//cuando vuelvo a ingresar paso por éste SINO... 
            $fechaFormato = $this->fecha(); //le agrego al array el resto de las fechas que usamos en la BD
        } else if ($id != '0' && $j == 0) {
            $fechaFormato = $valor; //este caso es solo por si ya viene un valor desde la BD es para la modificacioón
        } else {
            $fechaFormato = "0000-00-00 00:00:00"; //en el caso de ser un INSERT se llenan los campos fecha Modi y fecha baja con este campo
        }
        return $fechaFormato; //regreso la fecha *~{
    }

    public function buscarId($dato, $tabla) {//sirve para generar la sentencia que se encarga de buscar un id en la tabla 
        $strTabla = strtolower(substr($tabla, 11)); //al obtener de la clase el nombre de la clase de digo que quiero que parta la palabra controlador y me haga la consulta con el nombre del formulario
        $consulta = "SELECT * FROM " . $strTabla . " WHERE id_" . $strTabla . " = " . $dato; //ésta es la consulta ensambalda... tambien se prodria utilizar unida a un INNER JOIN todavía al momento de escribir esto todavía estoy pensando como hacerlo ;)
        return $consulta; //regreso la consulta
    }

    private function buscarInnerJoin($tabla,$id,$campo) {//Ésta función esta todavía en fase de prueba... en realidad lo que me gustaria hacer es ver si en lugar de pasar una tabla secundaria pudiera generar un array para poder pasar los datos de la vista... de esa manera podría leer las todos lo elementos que vienen en el array para poder armar la consulta °¬)
        $array = $this->meta($tabla); //armo el array para buscar las relaciones de la tabla
        $arrLlaveNum = $this->llaveNumerica($array);//cambio el array que obtengo desde la meta y lo convierto en numero para pasarlo por el for        
        $consulta = "SELECT * FROM ". $arrLlaveNum[0];//inicia la sentencia    
        for ($i = 0; $i < count($arrLlaveNum) - 1; $i++) {//uso un for para armar el array
            $consulta .= " INNER JOIN " . $arrLlaveNum[$i+1] . " ON " . $arrLlaveNum[0] . ".id_" . 
                    $arrLlaveNum[$i+1] . " = " . $arrLlaveNum[$i+1] . ".id_" . 
                    $arrLlaveNum[$i+1];//agrego cuantos campor sea necesario y genero la consulta con todos datos q vienen del array numerico
        }
        if ($id != NULL){
            $consulta.=" WHERE ".$arrLlaveNum[0].".".$campo." = '".$id."'";
        }
        return $consulta; // tal cual lo pienso regreso la función y... magicamente busca con un INNER JOIN  dentro de la BD... @)
    }

    public function buscarInnerJoinArray($tablaP, $arrayJoin) {//esta función es para armar una funcion para realizar la busqueda con un INNER JOIN
        $strTabla = substr($tablaP, 11); //elimino la primer parte del texto para obtener el nombre de la tabla
        strtolower($strTabla); //paso a minuscula elnombre de la tabla para que generar la consulta
        array_shift($arrayJoin); //elimino el primer elemento
        array_shift($arrayJoin); //elimino el siguiente elemento... tengo que ver si hay una forma mas efctiva de realizar esta funcion,,,, solo por si mando mas datos de la vista .. he pensado que prodria hacer un if donde pregunto si tienen un solo caracter para armar el array
        $consulta = " SELECT * FROM " . $strTabla; //empiezo armando la funcion
        foreach ($arrayJoin as $llave => $valor) {//leo el array 
            $consulta .= " INNER JOIN " . $valor . " ON " . 
                    $strTabla . ".id_" . $valor . " = " . $valor . ".id_" . $valor; //coloco los campos correspondientes en orden en el que vienen en el array que generé
        }
        $consulta .= " WHERE " . $strTabla . ".fch_baja = '0000-00-00 00:00:00'"; //finalizo la consulta llamando a la tabla que corresponde e indicando que la fecha de baja no sea 0 o lo que es lo mismo que no esté eliminada
        return $consulta; //regreso la consulta @}
    }

    public function verificarExistencia($tabla, $dato) {//esta funcion verifica si existe un campo especifico en la BD
        $array = $this->meta($tabla); //Traigo los datos de la BD        
        $arrayString = $this->arrayString($array); //ordeno los datos sacando el ID del array
        $strTabla = strtolower(substr($tabla, 11)); //paso a miniscula el nombre de la tabla
        $consulta = "SELECT COUNT(*) FROM " . $strTabla . " WHERE " . 
                $arrayString . " = '" . $dato . "'"; //genero la consulta con los datos 
        return $consulta; //regreso la consulta ^~)
    }

    public function armarSentenciaModificar($arrayCabecera, $tabla) {//ésta es la funcion encargada de generar la sentencia agregar en la base de datos
        $i = 1; //contador inicializado en 1 
        $strTabla = strtolower(substr($tabla, 11));
        //UPDATE combustible SET nombre_combustible=?,fch_modificacion=?,fch_baja=? WHERE id_combustible=?
        $sentencia = "UPDATE " . $strTabla . " SET"; //sentencia insert paso como dato el nombre de la tabla
        $llaveStr = ""; //inicializada en vacio, para poder cargarlo los campos de las base de dato a la sentencia
        array_shift($arrayCabecera);
        $long = sizeof($arrayCabecera); //determino el tamaño del array para usarlo en el if de los foreach
        foreach ($arrayCabecera as $llave => $value) {// recorro el array ;)
            $llaveStr = " " . $llave; //ingreso los nombres de los campos de la BD en la sentencia
            if ($long > $i) {//si todavia hay metadata con nombres de campo sigo el recorrido del array
                $sentencia .= $llaveStr . "=?,"; //agrego una coma despues de cada nuevo campo ingresado en la sentencia
            } else {//sino =)
                $sentencia .= $llaveStr . "=? "; //finalizo los campos en cerrando con un parentesis
            }
            $i++; //autoincremento de la variable
        }
        $sentencia .= ' WHERE id_' . $strTabla . " =?"; //concateno la sentencia con la cantidad de campos que se requieran para las incognitas
        return $sentencia;
    }

    public function arrayString($array) {//Ésta función la utilizo para  comparar la 1° clave de la base de datos (fuera del ID) con los datos que vienen desde la vista
        $i = 0; //inicio mi contador en 0
        $string = ""; //ésta es el string que voy a regresar a la consulta desde el controlador
        foreach ($array as $llave => $val) {//ingresa al foreach para recorrer el array
            if ($i == 1 && $val == "campo") {//verifica si es el 2 campo y si el contenido del mismo es campo ... ingreso y asigno los valores correspondientes al array
                $string = $llave; // el string que quiero devolver ;)
                break; //termino el ciclo ya tengo el dato que necesito
            } else if ($i == 1) {//sino :]
                $string = $val; // en este caso asigno el string lo cargo con el valor del campo porque es el que viene de la vista
            }
            $i++; //incremento
        }
        return $string; //regreso al lugar de donde se hizo la llamada... °¬[
    }

    public function cambiarArray($array) {//esta funcion es utilizada para cambiar el primer elemento por el último dentro de la funcion modficar
        $llave = array_keys($array)[0]; //creo una variable llamada llave donde guardo el primer elemento del array
        $valor = array_values($array)[0]; //una variable llamada valor donde guardo el primer valor del array
        array_shift($array); //elimino el primer elemento para no repetir $}
        $array[$llave] = $valor; //agrego al final del array el elemento q saque para que se utilice en la funcion modificar
        return $array; //regreso el array
    }

    public function armarCabecera($cabeceraArray) {//función pensada para armar un array que permita generar la cabecera en la vista
        foreach ($cabeceraArray as $llave => $valor) {
            echo "<br>" . $llave . "   " . $valor;
        }
    }

    public function verificarExistenciaUsuario($tabla, $user) {
        $strTabla = strtolower(substr($tabla, 11));
        return "SELECT COUNT(*) FROM " . $strTabla . " WHERE " . $strTabla . "_usuario = '" . $user . "'";
    }

    private function verificarJoin($tabla) {//funcion que se encarga de verificar si se puede hacer un join de acuerdo a las llaves "id_" que se obtienen de la bd
        $verifica = $this->meta($tabla);//array q contiene todos las llaves
        $contador = 0;//contador para determinar si las llaves "id_" se repiten mas de una vez si es ese el caso indica que hay join
        foreach ($verifica as $llave => $valor) {//recorro el array buscando los id´s
            if(substr($llave,0, 2)== "id"){//busco en el string de la llave el valor id
                $contador++;//sumo en el contador
            }
        }
        return $contador;//regreso el contador si es mayor a uno.. uso la funcion del join para crear la consulta
    }
    
    private function llaveNumerica($array) {//cambio los datos del array del meta por claves con numeros para manejar la sentencia del join
        $arrayNum = array(); //array generado para regresara al llamado de la funcion
        foreach ($array as $llave => $valor) {//for para recorrer el array
            if (substr($llave, 0, 2) == "id") {//solo ingreso si es id... en la base de datos todos los campos comunes deben comenzar con id y correspoderse con los id´s de las otras tablas
                array_push($arrayNum, substr($llave, 3)); //sumo a mi array nuevo los datos de las llavas sacando los "id_" que vienen de la BD
            }
        }
        return $arrayNum; //regreso el array
    }

    public function senteciaModificarUsuarioClave($tabla) {
        $strTabla = strtolower(substr($tabla, 11));
        $sentencia = "UPDATE ".$strTabla." SET clave_".$strTabla." =?, fch_modificacion = ? WHERE id_".$strTabla."=?";
        return $sentencia;
    }

}
