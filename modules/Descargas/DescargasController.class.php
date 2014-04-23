<?php

/**
 * Description of DescargasController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class DescargasController extends ControllerProject {

    protected $entity = "Descargas";

    public function IndexAction() {

        $this->values['seccionDescarga'] = ControllerWeb::getSubsecciones($this->request['IdEntity'],true);
        return parent::IndexAction();
    }

}

?>
