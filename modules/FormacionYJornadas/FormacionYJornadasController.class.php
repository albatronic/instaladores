<?php

/**
 * Description of FormacionYJornadasController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class FormacionYJornadasController extends ControllerProject {

    protected $entity = "FormacionYJornadas";

    public function IndexAction() {

        $this->values['formacionYJornadas'] = ControllerWeb::getSubsecciones($this->request['IdEntity'], true);

        return parent::IndexAction();
    }

    public function ListadoAction() {

        switch ($this->request['METHOD']) {
            case 'GET':
                $pagina = ($this->request[1]) ? $this->request[1] : 2;
                break;
            case 'POST':
                $pagina = $this->request['pagina'];
        }

        $seccion = ControllerWeb::getObjeto($this->request['Entity'], $this->request['IdEntity']);

        $this->values['seccion'] = array(
            'idSeccion' => $seccion->getPrimaryKeyValue(),
            'titulo' => $seccion->getTitulo(),
            'subtitulo' => $seccion->getSubtitulo(),
            'introducccion' => $seccion->getIntroduccion(),
            'contenidos' => Contenidos::getContenidosSeccion($this->request['IdEntity'], false, $pagina),
        );

        unset($seccion);

        return array('values' => $this->values, 'template' => $this->entity . '/listado.html.twig');
    }

}

?>
