<?php

/**
 * Description of IndexController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 06-nov-2012
 *
 */
class EventosController extends ControllerProject {

    protected $entity = "Eventos";

    public function IndexAction() {

        if ($this->request[1] == '')
            $this->request[1] = date();
        
        // Eventos del día $this->request[1]
        $this->values['eventos'] = Eventos::getEventos($this->request[1],$this->request[1]);

        return parent::IndexAction();
    }

}

?>
