<?php

/**
 * Description of ServiciosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class ServiciosController extends ControllerProject {

    protected $entity = "Servicios";

    public function IndexAction() {

        // Todos los servicios
        $this->values['servicios'] = Servicios::getServicios(1, -1);

        return parent::IndexAction();
    }
    
    public function ServicioAction() {

        $this->values['servicio'] = Servicios::getServicioDesarrollado($this->request['IdEntity']);
        
        return array(
            'values' => $this->values,
            'template' => $this->entity . '/servicioDesarrollado.html.twig');
    }

}

?>
