<?php

/**
 * CONTROLADOR DE PROYECTO. EXTIENDE AL CONTROLADOR WEB
 * 
 * El constructor realiza las tareas comunes al proyecto como por ej.
 * construir la ruta de navegación y los menús
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 26-nov-2012
 */
class ControllerProject extends ControllerWeb {

    public function __construct($request) {

        parent::__construct($request);

        $this->values['firma'] = $this->getFirma();     
        $this->values['redes'] = RedesSociales::getRedes();
        $this->values['datosContacto'] = $this->varWeb['Pro']['globales'];
        $this->values['ruta'] = ControllerWeb::getRuta();
        
        // Menú Cabecera para todas las páginas excepto el home
        $this->values['menuCabeceraGeneral'] = Menu::getMenuN(5,7);
        // Menú del pie
        $this->values['menuPie'] = Menu::getMenuN(3, 5);
        // Menú principal, el desplegable de la izquierda
        $this->values['menuDesplegable'] = Menu::getMenuDesplegable(1);
        
        $provincias = new Provincias();
        $this->values['provincias'] = $provincias->fetchAll('Provincia',false);
        unset($provincias);
        
        // Banners pie para todas las páginas
        $this->values['banners'] = Banners::getBanners(2,0,-1,5);  

    }
}

?>
