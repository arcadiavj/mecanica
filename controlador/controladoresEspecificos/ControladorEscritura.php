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
        $file = fopen("datos.sql", "w");
        fwrite($file, $escribir . PHP_EOL);
        var_dump($file);
        fclose($file);
    }

}
