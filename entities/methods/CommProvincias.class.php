<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 16.10.2012 16:33:55
 */

/**
 * @orm:Entity(CommProvincias)
 */
class CommProvincias extends CommProvinciasEntity {

    public function __toString() {
        return $this->getProvincia();
    }

    public function fetchAll($column = '', $default = TRUE) {
        if ($column == '')
            $column = 'Provincia';
        return parent::fetchAll($column, $default);
    }

}

?>