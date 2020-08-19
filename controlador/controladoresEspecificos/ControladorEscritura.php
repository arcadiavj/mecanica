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

        foreach ($escribir as $key => $value) {
            foreach ($value as $llave => $valor) {
                //var_dump($value);
                /* $file = fopen("../controladoresEspecificos/ControladorTiposervicio.txt", "r");
                  /* while (!feof($file)) {
                  var_dump(fgets($file));
                  if (strcasecmp(fgets($file), "class ControladorTiposervicio extends ControladorGeneral {") == 0) {
                  echo "entre";
                  //echo fgets($file) . "<br/>";
                  }
                  } */
                //fclose($file);
                //var_dump($value);

                $file = fopen("Controlador" . ucfirst($value["Table"]) . ".php", "w");
                fwrite($file, "<?php" . PHP_EOL);
                fwrite($file, "require_once 'ControladorGeneral.php';" . PHP_EOL);
                fwrite($file, "require_once 'ControladorMaster.php';" . PHP_EOL);
                fwrite($file, "class Controlador" . ucfirst($value["Table"]) . " extends ControladorGeneral {" . PHP_EOL);
                fwrite($file, "public function buscar() {" . PHP_EOL);
                fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "return \$master->buscar(\$tabla);" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);
                fwrite($file, "public function eliminar(\$id) {" . PHP_EOL);
                fwrite($file, "(string) \$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "\$master->eliminar(\$tabla, \$id);" . PHP_EOL);
                fwrite($file, "return ['eliminado'=>'eliminado'];" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);
                fwrite($file, " public function buscarUsuarioXId(\$dato) {" . PHP_EOL);
                fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "return \$master->buscarId(\$dato, \$tabla);" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);
                fwrite($file, " public function guardar(\$datosCampos) {" . PHP_EOL);
                fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "return \$master->guardar(\$tabla,\$datosCampos);" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);

                fwrite($file, " public function ultimo() {" . PHP_EOL);
                fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "return \$master->bucarUltimo(\$tabla);" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);

                fwrite($file, "public function modificar(\$datosCampos) {" . PHP_EOL);
                fwrite($file, "(string)\$tabla = get_class(\$this);" . PHP_EOL);
                fwrite($file, "\$master = new ControladorMaster();" . PHP_EOL);
                fwrite($file, "return \$master->modificar(\$tabla, \$datosCampos);" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);
                fwrite($file, "}" . PHP_EOL);


                fclose($file);
            }
            $this->escribiJS($value["Table"]);
        }

        //var_dump($file);
        //
    }

    public function escribiJS($tabla) {
        //var_dump($tabla);
        $sql = new SqlQuery();
        $array = $sql->meta("Controlador" . $tabla);
        $file = fopen("funciones" . ucfirst($tabla).".js", "w");
        fwrite($file, "$(function () {" . PHP_EOL);
        fwrite($file, "var TipoServicio = {};" . PHP_EOL);
        fwrite($file, "var idUsuario = \"\";" . PHP_EOL);
        fwrite($file, "(function (app) {" . PHP_EOL);
        fwrite($file, "app.init = function () {" . PHP_EOL);
        fwrite($file, " app.verificarSesion();" . PHP_EOL);
        fwrite($file, " };" . PHP_EOL);
        fclose($file);
        $this->desactivarControles($array, "funciones" . ucfirst($tabla).".js");  
                
    }

    public function desactivarControles($array, $f) {
        var_dump($f);
        $file = fopen("../ruteador/".$f, "a");
        fwrite($file, "app.desactivarControles = function () {" . PHP_EOL);
        foreach ($array as $key => $value) {
            fwrite($file, "$('#" . $key . "').attr('disabled', true);" . PHP_EOL);
        }
        fwrite($file, "}" . PHP_EOL);
        return fclose($file);
    }

}
