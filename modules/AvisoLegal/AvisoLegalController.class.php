<?php

/**
 * Description of AvisoLegalController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 20-agosto-2013
 *
 */
class AvisoLegalController extends ControllerProject {

    protected $entity = "AvisoLegal";

    public function IndexAction() {
        
        $this->values['datos'] = $this->varWeb['Pro']['globales'];
        
        return parent::IndexAction();
    }
}

?>
