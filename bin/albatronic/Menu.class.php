<?php

/**
 * CLASE ESTATICA PARA LA GESTION DE LOS MENUS
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright (c) Informática Albatronic, sl
 * @version 1.0 15-mar-2013
 */
class Menu {
    
    /**
     * Genera el array del menu indicado en $nMenu en base a las SECCIONES que:
     * 
     *      MostrarEnMenuN = 1
     * 
     * Los elementos del array son:
     * 
     * - etiquetaWeb => texto de la etiquetaWebN
     * - subetiquetaWeb => texto de la subetiquetaWebN
     * - titulo => el titulo de la seccion
     * - subtitulo => el subtitulo de la seccion
     * - url => array(url => texto, targetBlank => boolean)
     * - controller => texto con el nombre del controlador
     * 
     * @param int $nMenu El número de menu a mostrar (de 1 a 5). Por defecto 1
     * @param int $nItems El numero máximo de elementos a devolver. (0=todos)
     * @return array Array con las secciones que son menú
     */
    static function getMenuN($nMenu = 1, $nItems = 0) {

        $array = array();

        if (($nMenu < 1) or ($nMenu > 5))
            $nMenu = 1;
        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $seccion = new GconSecciones();
        $filtro = "MostrarEnMenu{$nMenu}='1'";
        $rows = $seccion->cargaCondicion("Id", $filtro, "OrdenMenu{$nMenu} ASC {$limite}");

        foreach ($rows as $row) {
            $seccion = new GconSecciones($row['Id']);
            $array[] = array(
                'etiquetaWeb' => $seccion->{"getEtiquetaWeb$nMenu"}(),
                'subetiquetaWeb' => $seccion->{"getSubetiquetaWeb$nMenu"}(),
                'titulo' => $seccion->getTitulo(),
                'subtitulo' => $seccion->getSubtitulo(),
                'url' => $seccion->getHref(),
                'controller' => $seccion->getObjetoUrlAmigable()->getController(),
            );
        }
        unset($seccion);

        return $array;
    }

    /**
     * Genera el array del menu desplegable en base a las secciones que:
     * 
     *      MostrarEnMenuN = 1
     *      BelongsTo = 0
     * 
     * Los elementos del array son:
     * 
     * - seccion => texto de la etiquetaWebN
     * - subetiquetaWeb => texto de la subetiquetaWebN
     * - url => array(url => texto, targetBlank => boolean)
     * - subsecciones => array de 0 a N (
     *                          titulo => texto
     *                          url => array(url => texto, targetBlank => boolean)
     *                      )
     *
     * @param int $nMenu El número de menu a mostrar (de 1 a 5). Por defecto 2
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional. (0=todos)
     * @return array Array con las secciones
     */
    static function getMenuDesplegable($nMenu = 2, $nItems = 0) {

        $array = array();

        if (($nMenu < 1) or ($nMenu > 5))
            $nMenu = 2;
        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $menu = new GconSecciones();
        $filtro = "MostrarEnMenu{$nMenu}='1' AND BelongsTo='0'";
        $rows = $menu->cargaCondicion("Id", $filtro, "OrdenMenu{$nMenu} ASC {$limite}");
        unset($menu);

        foreach ($rows as $row) {
            $subseccion = new GconSecciones($row['Id']);

            $array[] = array(
                'seccion' => $subseccion->{"getEtiquetaWeb$nMenu"}(),
                'subetiquetaWeb' => $subseccion->{"getSubetiquetaWeb$nMenu"}(),
                'url' => $subseccion->getHref(),
                'subsecciones' => $subseccion->getArraySubsecciones("OrdenMenu{$nMenu}", $nMenu),
            );
        }
        unset($subseccion);

        return $array;
    }
}

?>
