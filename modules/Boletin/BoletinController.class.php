<?php

/**
 * Description of AvisoLegalController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 20-agosto-2013
 *
 */
class BoletinController extends ControllerProject {

    protected $entity = "Boletin";

    public function IndexAction() {
        
        $boletin = new BolBoletines($this->request['IdEntity']);
              
        $this->values['boletin'] = array(
            'boletin' => $boletin,
            'contenidos' => $boletin->getContenidosRelacionados(),
        );
        unset($boletin);
        
        return parent::IndexAction();
    }
}

?>
