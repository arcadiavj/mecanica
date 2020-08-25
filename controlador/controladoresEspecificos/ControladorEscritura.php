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
            if($funciones[$index] == 'eliminar' 
               ||$funciones[$index] == 'buscarId'
               ||$funciones[$index] == 'guardar'
               ||$funciones[$index] == 'modificar'){
              fwrite($file, "public function " . $funciones[$index] . "(\$datos) {" . PHP_EOL);  
            } else{
               fwrite($file, "public function " . $funciones[$index] . "() {" . PHP_EOL); 
            }           
            fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
            fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
            if ($funciones[$index] == 'eliminar') {
                fwrite($file, "\$master->" . $funciones[$index] . "(\$tabla, \$datos);" . PHP_EOL);
                fwrite($file, "return ['eliminado'=>'eliminado'];" . PHP_EOL);
            } else if($funciones[$index] == 'buscarId'
                      ||$funciones[$index] == 'guardar'
                      ||$funciones[$index] == 'modificar'
                   ){
                    fwrite($file, "return \$master->" . $funciones[$index] . "(\$tabla, \$datos);" . PHP_EOL);
                   }else {
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
        fwrite($file, "var TipoServicio = {};" . PHP_EOL);
        fwrite($file, "var idUsuario = \"\";" . PHP_EOL);
        fwrite($file, "(function (app) {" . PHP_EOL);
        fwrite($file, "app.init = function () {" . PHP_EOL);
        fwrite($file, " app.verificarSesion();" . PHP_EOL);
        fwrite($file, " };" . PHP_EOL);
        fclose($file);
        $this->desactivarControles($array, "funciones" . ucfirst($tabla) . ".js");
        $this->activarControles($array, "funciones" . ucfirst($tabla) . ".js");
        $this->borrarCampos($array, "funciones" . ucfirst($tabla) . ".js");
        $this->eliminar(ucfirst($tabla));
        $this->borrarFila(ucfirst($tabla));
        $this->buscar(ucfirst($tabla));
        $this->rellenarDataTable($array, ucfirst($tabla));
    }

    public function borrarCampos($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.borrarCampos = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').val(\"\").html();" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function activarControles($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.activarControles = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').removeAttr('disabled');" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function desactivarControles($array, $f) {
        $file = fopen("../perCodere/" . $f, "a");
        fwrite($file, "app.desactivarControles = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').attr('disabled', true);" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

    public function eliminar($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.eliminar" . $t . " = function (id) {" . PHP_EOL);
        fwrite($file, "bootbox.confirm({" . PHP_EOL);
        fwrite($file, "size: 'medium'," . PHP_EOL);
        fwrite($file, "message: \"Esta Seguro que desea Eliminar el " . $t . "?\"," . PHP_EOL);
        fwrite($file, "callback: function (result) {" . PHP_EOL);
        fwrite($file, "if (result) {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/Ruteador.php?accion=eliminar&nombreFormulario=" . $t . "&id=\" + id;" . PHP_EOL);
        fwrite($file, "$.ajax({" . PHP_EOL);
        fwrite($file, "url: url," . PHP_EOL);
        fwrite($file, "method: \"GET\"," . PHP_EOL);
        fwrite($file, "dataType: 'json'," . PHP_EOL);
        fwrite($file, "success: function (data) {" . PHP_EOL);
        fwrite($file, "app.borrarFilaDataTable(id);" . PHP_EOL);
        fwrite($file, "}," . PHP_EOL);
        fwrite($file, "error: function (data) {" . PHP_EOL);
        fwrite($file, "alert('error');" . PHP_EOL);
        fwrite($file, "}" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "}" . PHP_EOL);
        fwrite($file, "}" . PHP_EOL);
        fwrite($file, "});" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fclose($file);
    }

    public function borrarFila($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.borrarFilaDataTable = function (id) {" . PHP_EOL);
        fwrite($file, "var fila = $(\"#cuerpo" . $t . "\").find(\"a[data-id_" . strtolower($t) . "='\" + id + \"']\").parent().parent().remove();" . PHP_EOL);
        fwrite($file, "};" . PHP_EOL);
        fclose($file);
    }

    public function buscar($t) {
        $file = fopen("../perCodere/funciones" . $t . ".js", "a");
        fwrite($file, "app.buscar" . $t . " = function () {" . PHP_EOL);
        fwrite($file, "var url = \"../../controlador/ruteador/Ruteador.php?accion=buscar&nombreFormulario=" . $t . "\";" . PHP_EOL);
        fwrite($file, "$.ajax({" . PHP_EOL);
        fwrite($file, "url: url," . PHP_EOL);
        fwrite($file, "method: 'GET'," . PHP_EOL);
        fwrite($file, "dataType: 'json'," . PHP_EOL);
        fwrite($file, "success: function (data) {" . PHP_EOL);
        fwrite($file, "app.rellenarDataTable(data);" . PHP_EOL);
        fwrite($file, "}," . PHP_EOL);
        fwrite($file, "error: function (data) {" . PHP_EOL);
        fwrite($file, "alert('error en buscar " . $t . "');" . PHP_EOL);
        fwrite($file, "}," . PHP_EOL);
        fwrite($file, "beforeSend: function (){" . PHP_EOL);
        fwrite($file, "var dialog = bootbox.dialog({" . PHP_EOL);
        fwrite($file, "message:\"<p class='text-center'><img src='../../vista/images/ajax-loader.gif'></p>\"," . PHP_EOL);
        fwrite($file, "closeButton: false});" . PHP_EOL);
        fwrite($file, "dialog.modal('hide');}});};" . PHP_EOL);
        fclose($file);
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

}
