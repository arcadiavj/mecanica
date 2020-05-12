<?php

/*
 *  Ruteador Generado por Diego para eliminar los WARNING que dejaba PHP  por ingresar a los datos
 *  directamente desde las variables globales....
 *  la función es la misma que el anterior por haciendo uso de los filter_input y filter_input_array
 *  para cargar los datos de las variables donde corresponden 
 */
$arrayParamGet = filter_input_array(INPUT_GET); //creo un Array con los datos q filtro desde el HTML que vienen con el metodo GET
$arrayParamPost = filter_input_array(INPUT_POST); //creo un Array con los datos q filtro desde el HTML que vienen con el metodo GET
if ($arrayParamGet != NULL) {//si los datos vienen por GET ingresa en este if
    $accion = filter_input(INPUT_GET, 'accion'); //filtrando datos cargo la variable accion
    $nombreformulario = filter_input(INPUT_GET, 'nombreFormulario'); //filtrando datos creo la variable nombreFormulario     
} else if ((array_key_exists('accion', $arrayParamPost))) {//si viene por POST compruebo q tenga la llave accion para enviar al switch
    $accion = filter_input(INPUT_POST, 'accion'); //cargo con el filtrado input de las variables la accion que va hacia el metodo switch
    $datosCampos = filter_input_array(INPUT_POST); //cargo con el input_array todos los datos que llegan por POST en la variable $datosCampos q luego deberan ser enviados al switch
    $nombreformulario = filter_input(INPUT_POST, 'nombreFormulario'); //cargo con el filtrado input de las variables la accion que va hacia el metodo switch
    $id = $arrayParamPost["id"];
} else if ((array_key_exists('user', $arrayParamPost)) && (array_key_exists('pass', $arrayParamPost))) {//entra en esta sección del if si existen las variables user y pass
    $accion = "guardar"; //si entró en este If la accion correspondiente es la de guardar
    $nombreformulario = "Usuario"; //los mismo sucede con el nombre de formulario que es Usuario
    /* $user = filter_input(INPUT_POST, 'user');//cargo la variable user con los datos fitrados desde el HTML
      $pass = filter_input(INPUT_POST, 'pass');//cargo la variable pass con los datos fitrados desde el HTML
      $datosCampos = ["user" => $user, "pass" => $pass];//en realidad no se por que uso estos datos y los de las lineas 15 y 14 por q de ellos no obtendria lo mismo que con la siguiente linea de código */
    $datosCampos = filter_input_array(INPUT_POST); //cargo un array con los datos enviados por POST desde el HTML y evito las tres líneas de código anteriores...
} 

require_once '../controladoresEspecificos/Controlador' . $nombreformulario . '.php'; //hago el include del controlador correspondiente
$nombreControlador = "Controlador" . $nombreformulario; //Genero una variable con el nombre del controlador para poder generar un objeto del mismo
$objControlador = new $nombreControlador(); //instancio el objeto correpondiente al controlador creado
switch ($accion) {//utilizo la accion que se carga desde el HTML para poder realizar la acción adecuada y solicitada por el usuario desde la vista HTML
    case "actualizar":
        $resultado = $objControlador->$accion(); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "eliminar":
        $id = $arrayParamGet["id"];
        $resultado = $objControlador->$accion($id); //llamo a la acción desde el objeto instanciado anteriormente al ser una eliminacion logica necesito el id del elemento a eliminar
        echo json_encode($resultado); //arreglo json
        break;
    case "buscar":
        $resultado = $objControlador->$accion(); //llamo a la acción desde el objeto instanciado anteriormente 
        echo json_encode($resultado); //arreglo json
        break;
    case "buscarJoin":
        $resultado = $objControlador->$accion($datosCampos); //llamo a la acción desde el objeto instanciado anteriormente 
        echo json_encode($resultado); //arreglo json
        break;
    case "guardar":  
        $resultado = $objControlador->$accion($datosCampos, $nombreformulario); //llamo a la acción desde el objeto instanciado anteriormente, es esta acción paso los datos correspondientes al array que generé con los datos enviados desde el HTML
        echo json_encode($resultado); //arreglo json
        break;
    case "cambiarClave":
        $resultado = $objControlador->$accion($idCliente); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "modificar":
        $resultado = $objControlador->$accion($datosCampos, $nombreformulario); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break; //final cambio diego
    case "buscarXId":
        $resultado = $objControlador->$accion($datos); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "getFac":
        $id = $arrayParamGet["id"];
        $resultado = $objControlador->$accion($id); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "getMovil":
        $resultado = $objControlador->$accion($id); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "agregar":
        $resultado = $objControlador->$accion($id); //llamo a la acción desde el objeto instanciado anteriormente
        echo json_encode($resultado); //arreglo json
        break;
    case "cabecera":
        $resultado = $objControlador->$accion(); //llamo a la acción desde el objeto instanciado anteriormente   
        echo json_encode($resultado); //arreglo json
        break;
    case "buscarSinExpte":
        $resultado = $objControlador->$accion(); //llamo a la acción desde el objeto instanciado anteriormente   
        echo json_encode($resultado); //arreglo json
        break;
    case "buscarUsuarioXId":
        $id = $arrayParamGet["id"];
        $resultado = $objControlador->$accion($id); //llamo a la acción desde el objeto instanciado anteriormente   
        echo json_encode($resultado); //arreglo json
        break;
        
    
    default:
        break;
}