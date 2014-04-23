<?php

/**
 * Description of NoticiasController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class NoticiasController extends ControllerProject {

    protected $entity = "Noticias";

    public function IndexAction() {

        $pagina = ($this->request['METHOD'] == 'POST') ?
                $this->request['pagina'] :
                1;

        /* NOTICIAS - mostrar noticias que no son portada porque ya se muestran en el home */
        $this->values['noticias'] = Noticias::getNoticias(false, 0, $pagina);

        return parent::IndexAction();
    }

}

?>
