<?php

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

    public function escribir($escribir) {

        foreach ($escribir as $key => $value) {
            foreach ($value as $llave => $valor) {
                var_dump($value);
                $file = fopen("../controladoresEspecificos/ControladorTiposervicio.txt", "r");
                while (!feof($file)) {
                    var_dump(fgets($file));
                    if (strcasecmp(fgets($file), "class ControladorTiposervicio extends ControladorGeneral {") == 0) {
                        echo "entre";
//echo fgets($file) . "<br/>";
                    }
                }
                fclose($file);
                //var_dump($value);
                $file = fopen($value["Table"] . ".php", "w");
                fwrite($file, $llave . " " . $valor . PHP_EOL);
                fclose($file);
            }
        }

        //var_dump($file);
        //
    }

}
