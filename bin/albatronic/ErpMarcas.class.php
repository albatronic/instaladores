<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE MARCAS/FABRICANTES
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 3-jun-2013
 */
class ErpMarcas {

    /**
     * Devuelve un objeto marca/fabricante
     * 
     * @param integer $idMarca el id de la marca
     * @return \Fabricantes
     */
    static function getMarca($idMarca) {
        return new Fabricantes($idMarca);
    }
    
    /**
     * Devuelve un array (Id,Value) con todas las marcas
     * @return array
     */
    static function getMarcas() {
        $marcas = new Fabricantes();
        $rows = $marcas->fetchAll('',false);
        unset($marcas);
        
        return $rows;
    }
}

?>
