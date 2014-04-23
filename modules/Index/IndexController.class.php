<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 06-nov-2012
 *
 */
class IndexController extends ControllerProject {

    protected $entity = "Index";

    public function IndexAction() {

        $this->values['menuCabeceraIzq'] = Menu::getMenuN(2, 3);
        $this->values['menuCabeceraDcha'] = Menu::getMenuN(4, 3);

        // Sliders
        $this->values['sliders'] = Sliders::getSliders(1);
        
        // NOTICIAS - mostrar las 5 primeras noticias de portada
        $this->values['noticia'] = Noticias::getNoticias(true);
        
        $this->values['calendario'] = Calendario::getCalendario();
        
        // Banners de la derecha (Zona de banners = 1)
        $this->values['banners'] = Banners::getBanners(1,0,-1,6);

        return parent::IndexAction();
    }

}

?>
