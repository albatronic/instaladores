<?php

/**
 * Description of AsociacionesController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 20-Agosto-2013
 *
 */
class AsociacionesController extends ControllerProject {

    protected $entity = "Asociaciones";

    public function IndexAction() {
        
        $this->values['contenidos'] = Contenidos::getContenidosSeccion($this->request['IdEntity']);
        
        return parent::IndexAction();
    }
}

?>
