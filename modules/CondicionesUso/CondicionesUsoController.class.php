<?php

/**
 * Description of CondicionesUsoController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 27-jun-2013
 *
 */
class CondicionesUsoController extends ControllerProject {

    protected $entity = "CondicionesUso";

    public function IndexAction() {
        
        $this->values['dominio'] = $this->varWeb['Pro']['globales']['dominio'];
        $this->values['empresa'] = $this->varWeb['Pro']['globales']['empresa'];
        
        return parent::IndexAction();
    }
}

?>
