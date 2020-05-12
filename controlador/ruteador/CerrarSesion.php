<?php

    $array = filter_input_array(INPUT_POST);
    require_once '../controladoresEspecificos/ControladorUsuario.php';
    require_once './Sesion.php';
    $cU = new ControladorUsuario();
    $respuesta =$cU->cerrarSesion($array); 
    session_destroy();   
    
    
