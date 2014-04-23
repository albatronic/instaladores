<?php

/**
 * Description of PoliticaPrivacidadController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, sl
 * @date 26-nov-2012
 *
 */
class PoliticaPrivacidadController extends ControllerProject {

    protected $entity = "PoliticaPrivacidad";

    public function IndexAction() {
        
        $this->values['datos'] = $this->varWeb['Pro']['globales'];
        
        return parent::IndexAction();
    }    
}

?>
