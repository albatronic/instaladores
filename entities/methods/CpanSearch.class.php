<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 18.04.2013 13:10:03
 */

/**
 * @orm:Entity(CpanDocs)
 */
class CpanSearch extends CpanSearchEntity {

    public function save() {
        $this->Publish = 1;
        return parent::save();
    }

    public function create() {
        $this->Publish = 1;
        return parent::create();
    }

    public function actualiza($objeto) {
     
        $entidad = $objeto->getClassName();
        $idEntidad = $objeto->getPrimaryKeyValue();

        $this->queryDelete("Entity='{$entidad}' and IdEntity='$idEntidad'");

        if ( ($objeto->getPublish()->getIDTipo() == '1') and ($objeto->getDeleted()->getIDTipo() == '0') ) {
            $variables = CpanVariables::getVariables("Env", "Mod", "Articulos");
            foreach ($variables['columns'] as $columna => $atributos)
                if ($atributos['searchable']) {
                    $texto = $objeto->{"get$columna"}();
                    if ($texto) {
                        $search = new CpanSearch();
                        $search->setTexto($texto);
                        $search->setEntity($entidad);
                        $search->setIdEntity($idEntidad);
                        $search->setChecked($objeto->getChecked()->getIDTipo());
                        $search->setPrivacy($objeto->getPrivacy()->getIDTipo());
                        $search->create();
                    }
                }
        }
    }

}

?>