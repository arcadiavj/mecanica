<?php

//include "SqlQuery.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControladorEscritura
 *
 * @author DIEGO
 */
class ControladorEscritura {

    public function escribirPHP($escribir) {

        $funciones = ['buscar', 'eliminar', 'buscarId', 'guardar', 'bucarUltimo', 'modificar'];
        foreach ($escribir as $key => $value) {
            $file = fopen("../perCodere/Controlador" . ucfirst($value["Table"]) . ".php", "w");
            fwrite($file, "<?php" . PHP_EOL);
            fwrite($file, "require_once 'ControladorGeneral.php';" . PHP_EOL);
            fwrite($file, "require_once 'ControladorMaster.php';" . PHP_EOL);
            fwrite($file, "class Controlador" . ucfirst($value["Table"]) . " extends ControladorGeneral {" . PHP_EOL);
            fclose($file);
            $this->funciones($funciones,"../perCodere/Controlador" . ucfirst($value["Table"]) . ".php");
            $f=fopen("../perCodere/Controlador" . ucfirst($value["Table"]) . ".php", "a");
            fwrite($f, "}" . PHP_EOL);
            fclose($f);
            $this->escribiJS($value["Table"]);
        }
    }

    public function funciones($funciones, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        for ($index = 0; $index < count($funciones); $index++) {
            if ($funciones[$index] == 'eliminar' || $funciones[$index] == 'buscarId' || $funciones[$index] == 'guardar' || $funciones[$index] == 'modificar') {
                fwrite($file, "public function " . $funciones[$index] . "(\$datos) {" . PHP_EOL);
            } else {
                fwrite($file, "public function " . $funciones[$index] . "() {" . PHP_EOL);
            }
            fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
            fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
            if ($funciones[$index] == 'eliminar') {
                fwrite($file, "\$master->" . $funciones[$index] . "(\$tabla, \$datos);" . PHP_EOL);
                fwrite($file, "return ['eliminado'=>'eliminado'];" . PHP_EOL);
            } else if ($funciones[$index] == 'buscarId' || $funciones[$index] == 'guardar' || $funciones[$index] == 'modificar'
            ) {
                fwrite($file, "return \$master->" . $funciones[$index] . "(\$tabla, \$datos);" . PHP_EOL);
            } else {
                fwrite($file, "return \$master->" . $funciones[$index] . "(\$tabla);" . PHP_EOL);
            }
            fwrite($file, "}" . PHP_EOL);
        }
        fclose($file);
    }

    public function escribiJS($tabla) {
//var_dump($tabla);
        $sql = new SqlQuery();
        $array = $sql->meta("Controlador" . $tabla);
        $file = fopen("../perCodere/funciones" . ucfirst($tabla) . ".js", "w");
        fwrite($file, "$(function () {" . PHP_EOL);
        fwrite($file, "var ". ucfirst($tabla)." = {};" . PHP_EOL);
        fwrite($file, "var idUsuario = \"\";" . PHP_EOL);
        fwrite($file, "(function (app) {" . PHP_EOL);
        fwrite($file, "app.init = function () {" . PHP_EOL);
        fwrite($file, " app.verificarSesion();" . PHP_EOL);
        fwrite($file, " };" . PHP_EOL);
        fclose($file);
        $this->bindings("funciones" . ucfirst($tabla) . ".js");
        $this->cerrarSesion("funciones" . ucfirst($tabla) . ".js");
        $this->desactivarControles($array, "funciones" . ucfirst($tabla) . ".js");
        $this->activarControles($array, "funciones" . ucfirst($tabla) . ".js");
        $this->borrarCampos($array, "funciones" . ucfirst($tabla) . ".js");
        $this->eliminar(ucfirst($tabla));
        $this->borrarFila(ucfirst($tabla));
        $this->buscar(ucfirst($tabla));
        $this->rellenarDataTable($array, ucfirst($tabla));
        $this->verificarSesion(ucfirst($tabla));
        $this->appBinding($array, $tabla);
        
    }
    public function cerrarSesion($tabla){
        $file = fopen("../perCodere/" . $tabla, "a");
        fwrite($file, "app.".__FUNCTION__." = function (event) {" . PHP_EOL);
        fwrite($file, "event.preventDefault();" . PHP_EOL);
        fwrite($file, "bootbox.confirm({" . PHP_EOL);
        fwrite($file, "size: 'medium'," . PHP_EOL);
        fwrite($file, "message: 'Esta seguro que desea finalizar sus sesion de trabajo?'," . PHP_EOL);
        fwrite($file, "callback: function (result) {" . PHP_EOL);
        fwrite($file, "if (result) {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/CerrarSesion.php\";" . PHP_EOL);
        fwrite($file, "var datosEnviar = {usuario: idUsuario};" . PHP_EOL);
        fclose($file);
        $this->ajax("../perCodere/" . $tabla ,"post");
        $f=fopen("../perCodere/" . $tabla, "a");
        fwrite($f, "success: function (datosDevueltos) {" . PHP_EOL);
        fwrite($f, "document.location.href = \"../../index.html\";" . PHP_EOL);
        fwrite($f, " }," . PHP_EOL);
        fwrite($f, "error: function () {" . PHP_EOL);
        fwrite($f, " alert(\"error al enviar al servidor\");" . PHP_EOL);
        fwrite($f, " }" . PHP_EOL);
        fwrite($f, " });" . PHP_EOL);
        fwrite($f, " }" . PHP_EOL);
        fwrite($f, " }" . PHP_EOL);
        fwrite($f, " });" . PHP_EOL);
        fwrite($f, " };" . PHP_EOL);
        fwrite($f, " }" . PHP_EOL);
    }
    public function bindings($tabla) {
        $file = fopen("../perCodere/" . $tabla, "a");
        fwrite($file, "app.bindings = function () {" . PHP_EOL);
        fwrite($file, "$(\"#cerrarSesion\").on('click', function (event) {" . PHP_EOL);
        fwrite($file, "alert(idUsuario);" . PHP_EOL);
        fwrite($file, "app.cerrarSesion(event);" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "$(\"#salir\").on('click', function (event) {" . PHP_EOL);
        fwrite($file, "app.cerrarSesion(event);" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fwrite($file, "}" . PHP_EOL);
        fclose($file);
    }

    public function borrarCampos($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.".__FUNCTION__." = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').val(\"\").html();" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function activarControles($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.".__FUNCTION__." = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').removeAttr('disabled');" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function desactivarControles($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.".__FUNCTION__." = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').attr('disabled', true);" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function eliminar($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.".__FUNCTION__. $t . " = function (id) {" . PHP_EOL);
        fwrite($file, "bootbox.confirm({" . PHP_EOL);
        fwrite($file, "size: 'medium'," . PHP_EOL);
        fwrite($file, "message: \"Esta Seguro que desea Eliminar el " . $t . "?\"," . PHP_EOL);
        fwrite($file, "callback: function (result) {" . PHP_EOL);
        fwrite($file, "if (result) {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/Ruteador.php?accion=eliminar&nombreFormulario=" . $t . "&id=\" + id;" . PHP_EOL);
        fclose($file);
        $this->ajax("../perCodere/funciones" . $t . ".js","get");
        $f=fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($f, "success: function (data) {" . PHP_EOL);
        fwrite($f, "app.borrarFilaDataTable(id);" . PHP_EOL);
        fwrite($f, "}," . PHP_EOL);
        fwrite($f, "error: function (data) {" . PHP_EOL);
        fwrite($f, "alert('error');" . PHP_EOL);
        fwrite($f, "}" . PHP_EOL);
        fwrite($f, "});" . PHP_EOL);
        fwrite($f, "}" . PHP_EOL);
        fwrite($f, "}" . PHP_EOL);
        fwrite($f, "});" . PHP_EOL);
        fwrite($f, "};" . PHP_EOL);
        fclose($f);
    }

    public function borrarFila($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.".__FUNCTION__."DataTable = function (id) {" . PHP_EOL);
        fwrite($file, "var fila = $(\"#cuerpo" . $t . "\").find(\"a[data-id_" . strtolower($t) . "='\" + id + \"']\").parent().parent().remove();" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fclose($file);
    }

    public function buscar($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.".__FUNCTION__. $t . " = function () {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=" . $t . "\";" . PHP_EOL);
        fclose($file);
        $this->ajax("../perCodere/funciones" . $t . ".js","get");
        $f=fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($f, "success: function (data) {" . PHP_EOL);
        fwrite($f, "app.rellenarDataTable(data);" . PHP_EOL);
        fwrite($f, "}," . PHP_EOL);
        fwrite($f, "error: function (data) {" . PHP_EOL);
        fwrite($f, "alert('error en buscar " . $t . "');" . PHP_EOL);
        fwrite($f, "}," . PHP_EOL);
        fwrite($f, "beforeSend: function (){" . PHP_EOL);
        fwrite($f, "var dialog = bootbox.dialog({" . PHP_EOL);
        fwrite($f, "message:\"<p class='text-center'><img src='../../vista/images/ajax-loader.gif'></p>\"," . PHP_EOL);
        fwrite($f, "closeButton: false});" . PHP_EOL);
        fwrite($f, "dialog.modal('hide');}});};" . PHP_EOL);
        fclose($f);
    }

    public function rellenarDataTable($array, $t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.rellenarDataTable = function (data) {" . PHP_EOL);
        fwrite($file, "var html = \"\";" . PHP_EOL);
        fwrite($file, "if ($.fn.DataTable.isDataTable('#tablaTiposervicio')) {" . PHP_EOL);
        fwrite($file, "$('#tablaTiposervicio').DataTable().destroy();}" . PHP_EOL);
        fwrite($file, "$.each(data, function (clave, " . strtolower($t) . ") {" . PHP_EOL);
        fwrite($file, "html += '<tr class=\"text-warning\">' +" . PHP_EOL);
        fwrite($file, "'<td><a class=\"seleccionar\" data-id_" . strtolower($t) . "=\"' +" . strtolower($t) . ".id_" . strtolower($t) . "+'\"><button class=\"btn btn-info btn-sm\">' +" . PHP_EOL);
        fwrite($file, "'<span class=\"glyphicon glyphicon-eye-open left\">  Info</span></button></a></td>' +" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "'<td>' + " . strtolower($t) . "." . $key . "_" . strtolower($t) . " + '</td>' +" . PHP_EOL);
        }
        fwrite($file, "'<a class=\"pull-left editar\" data-id_" . strtolower($t) . "=\"' + " . strtolower($t) . ".id_" . strtolower($t) . " + '\"><button class=\"btn btn-success btn-sm\">' +" . PHP_EOL);
        fwrite($file, "'<span class=\"glyphicon glyphicon-pencil\"> Editar</span></button></a>' +" . PHP_EOL);
        fwrite($file, "'<a class=\"pull-right eliminar\" data-id_" . strtolower($t) . "=\"' + " . strtolower($t) . ".id_" . strtolower($t) . " + '\"><button class=\"btn btn-danger btn-sm\">' +" . PHP_EOL);
        fwrite($file, "'<span class=\"glyphicon glyphicon-remove\"> Eliminar</span></button></a>' +" . PHP_EOL);
        fwrite($file, "'</td>' +" . PHP_EOL);
        fwrite($file, "'</tr>';" . PHP_EOL);
        fwrite($file, "$(\"#cuerpo" . $t . "\").html(html);" . PHP_EOL);
        fwrite($file, "$(\"#tabla" . $t . "\").dataTable({" . PHP_EOL);
        fwrite($file, "responsive: true," . PHP_EOL);
        fwrite($file, "\"sPagiationType\": \"full_numbers\"," . PHP_EOL);
        fwrite($file, "\"language\": {" . PHP_EOL);
        fwrite($file, "\"url\": \"../js/dataTable-es.json\"" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "$(\".oculto\").hide();" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fclose($file);
    }
    public function verificarSesion($tabla){
        $file = fopen("../perCodere/funciones" . $tabla . ".js", "a");
        fwrite($file, "app.verificarSesion = function () {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/Sesion.php\";" . PHP_EOL);
        fclose($file);
        $this->ajax("../perCodere/funciones" . $tabla . ".js","post");
        $f=fopen("../perCodere/funciones" . $tabla . ".js", "a");
        fwrite($f, "success: function (datos) {" . PHP_EOL);
        fwrite($f, "if (typeof datos['id_usuario'] != 'undefined' && typeof datos['tipoAcceso_usuario'] != 'undefined') {" . PHP_EOL);
        fwrite($f, "$(\"#id_user\").val(datos.id_usuario);" . PHP_EOL);
        fwrite($f, "var tA = datos.tipoAcceso_usuario;" . PHP_EOL);        
        fwrite($f, "idUsuario = datos.id_usuario;" . PHP_EOL);
        fwrite($f, "if (parseInt(tA) === 1 || parseInt(tA) === 2 ) {" . PHP_EOL);
        fwrite($f, "$(\"#logedUser\").html(datos.usuario_usuario);" . PHP_EOL);
        fwrite($f, "var acceso = datos.tipoAcceso_usuario;" . PHP_EOL);
        fwrite($f, "app.buscar".$tabla."();" . PHP_EOL);
        fwrite($f, "app.bindings();" . PHP_EOL);
        fwrite($f, "app.cargarBotones(acceso);" . PHP_EOL);
        fwrite($f, "} else {" . PHP_EOL);
        fwrite($f, "location.href = \"../../admin.html\";" . PHP_EOL);
        fwrite($f, "} else {" . PHP_EOL);
        fwrite($f, "location.href = \"../../index.html\";" . PHP_EOL);
        fwrite($f, "}" . PHP_EOL);
        fwrite($f, "app.verificarSesion = function () {" . PHP_EOL);
        fwrite($f, "}}," . PHP_EOL);
        fwrite($f, "error: function (data) {" . PHP_EOL);
        fwrite($f, "location.href = \"../../index.html\";" . PHP_EOL);        
        fwrite($f, " }});};" . PHP_EOL);   
    }
    
    public function ajax($f, $tipo) {
        $file = fopen($f, "a");
        fwrite($file, "$.ajax({" . PHP_EOL);
        fwrite($file, "url: url," . PHP_EOL);
        if ($tipo == 'get') {
            fwrite($file, "method: 'GET'," . PHP_EOL);
        } else {
            fwrite($file, "method: 'POST'," . PHP_EOL);
        }
        fwrite($file, "dataType: 'json'," . PHP_EOL);
        fclose($file);
    }
    
    public function appBinding($array, $tabla){
       
         $file = fopen("../perCodere/funciones" . $tabla . ".js", "a");
         fwrite($file, "app.bindings = function () {" . PHP_EOL);
         foreach ($array as $key => $value) {
             if(substr($key, 0,3)=='fch' ){
                fwrite($file, "$('#txt".ucfirst(substr($key, 4))."').attr('disabled', true);" . PHP_EOL);
             }
             
        }
         fwrite($file, "$('#agregar').on('click', function (event) {" . PHP_EOL);
         fwrite($file, "event.preventDefault();" . PHP_EOL);
         fwrite($file, "app.activarControles();" . PHP_EOL);
         foreach ($array as $key => $value) {
             if(substr($key, 0,3)=='fch' ){
                fwrite($file, "$('#div".ucfirst(substr($key, 4))."').hide();" . PHP_EOL);
             }
             
        }
        fwrite($file, "$('#id').val(0);" . PHP_EOL);
        fwrite($file, "$('#mHeader').removeClass();" . PHP_EOL);
        fwrite($file, "$('#mHeader').attr(\"class\", \"modal-header bg-primary\");" . PHP_EOL);
        fwrite($file, "$('#tituloModal').html(\"Nuevo".ucfirst($tabla)."\");" . PHP_EOL);
        fwrite($file, "$('#modal".ucfirst($tabla)."').modal({show: true, backdrop: 'static', keyboard: false});" . PHP_EOL);
        fwrite($file, "$('#accion').attr(\"value\", \"guardar\");" . PHP_EOL);
        fwrite($file, "$('#guardar').html(\"Agregar\");" . PHP_EOL);
        fwrite($file, "$('#guardar').show();" . PHP_EOL);
        fwrite($file, "$('#reporDetalle').hide();" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "$('#modal".ucfirst($tabla)."').on('shown.bs.modal', function () {" . PHP_EOL);
        fwrite($file, "$('#nombre".ucfirst($tabla)."').focus();" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "$('#cuerpo".ucfirst($tabla)."').on('click', '.editar', function (event) {" . PHP_EOL);
        fwrite($file, "event.preventDefault();" . PHP_EOL);
        fwrite($file, "$('#id').val($(this).attr(\"data-id_".$tabla."\"));" . PHP_EOL);
        // TODO: Hacer funcion foreach para recorrer campo fecha
        foreach ($array as $key => $value) {
             if(substr($key, 0,3)=='fch' ){
                fwrite($file, "$('#div".ucfirst(substr($key, 4))."').show();" . PHP_EOL);
             }             
        }
        fwrite($file, "$('#mHeader').removeClass();" . PHP_EOL);
        fwrite($file, "$('#mHeader').attr('class', 'modal-header bg-success');" . PHP_EOL);
        $i=0;
        var_dump($array);
        foreach ($array as $key => $value) {
            $nombre ="";
            $next = "";            
             if(substr($key, 0,3)=='fch' ){
               $i=0; $nombre= substr($key, 4);
                $i++;
             }else{
                $nombre = $key;
                $i++;
             }
             for ($index = 0; $index < $i; $index++) {
               $next .= "next().";
            }
           fwrite($file, "$('#".$nombre."').val($(this).parent().parent().children().first().".$next."html());" . PHP_EOL);
           //echo "$('#".$nombre."').val($(this).parent().parent().children().first().".$next."html());<br>";
        }
        fwrite($file, "app.activarControles();" . PHP_EOL);
        fwrite($file, "$('#guardar').html('Modificar');" . PHP_EOL);
        fwrite($file, "$('#accion').attr('value', 'modificar');" . PHP_EOL);
        fwrite($file, "$('#tituloModal').html(\"Editar".ucfirst($tabla)."\");" . PHP_EOL);   
        fwrite($file, "$('#modal".ucfirst($tabla)."').modal({show: true, backdrop: 'static', keyboard: false});" . PHP_EOL);
        fwrite($file, "$('#guardar').show();" . PHP_EOL);
        fwrite($file, "$('#reporDetalle').hide();" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
    }

}

