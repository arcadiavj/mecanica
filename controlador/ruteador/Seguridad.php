<?php
/* Seguridad generado con el uso del filter_input...
 * para eliminar el uso de las variables globales $_GET... $_POST... $_REQUEST...
 * */
//if (isset($_POST['accion'])) {
$arrayParamGet = filter_input_array(INPUT_GET);//genero un array con el filter_input_array para cargar en el mismo todos los datos que vienen por GET desde el HTML
$arrayParamPost = filter_input_array(INPUT_POST);//genero un array con el filter_input_array para cargar en el mismo todos los datos que vienen por POST desde el HTML

if (array_key_exists('accion', $arrayParamPost)) {//si dentro del array existe la llave accion ingresa en este if
    
    require_once '../controladoresEspecificos/ControladorUsuario.php';
    $cU = new ControladorUsuario();
    $paramCambiarClave = ["clave_usuario" => base64_decode(base64_decode($arrayParamPost["nuevoPass"])), 
        "id_usuario" => $arrayParamPost["id_usuario"]];
    $respuesta = $cU->cambiarClave($paramCambiarClave);
    echo json_encode($respuesta);
} else if (array_key_exists('user', $arrayParamPost)) {
    $usuario = filter_input(INPUT_POST,'user');    
    if (array_key_exists('pass',$arrayParamPost)) {
        $clave = filter_input(INPUT_POST,'pass');
        require_once '../controladoresEspecificos/ControladorUsuario.php';
        $cU = new ControladorUsuario();
        $us = base64_decode(base64_decode($usuario));
        $pa = base64_decode(base64_decode($clave));
        $res = $cU->validarUsuarioClave($us, $pa);
        echo json_encode($res);
    }else{
        echo "Estoy en el else del If en donde entramos esperando que las cosas sean como quiero";
    }
}

