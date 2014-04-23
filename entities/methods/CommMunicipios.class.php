<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 18.10.2012 00:21:37
 */

/**
 * @orm:Entity(CommMunicipios)
 */
class CommMunicipios extends CommMunicipiosEntity {

    public function __toString() {
        return $this->Municipio;
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'Municipio';
        return parent::fetchAll($column, $default);
    }

}

?>