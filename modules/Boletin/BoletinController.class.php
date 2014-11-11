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
    
    public function ListadoAction() {
        
        $boletin = new BolBoletines();
        
        $rows = $boletin->cargaCondicion("Id","1","PublishedAt DESC");
        unset($boletin);
        
        foreach ($rows as $row) {
            $boletines[] = new BolBoletines($row['Id']);
        }

        $this->values['boletines'] = $boletines;
        
        return array(
            'values' => $this->values,
            'template' => 'Boletin/listado.html.twig',
        );
    }
}

?>
