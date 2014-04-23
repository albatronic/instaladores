<?php

/**
 * Description of Rangos de precios
 *
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 15-may-2013 00:41:00
 */
class RangosPrecios extends Tipos {

    protected $tipos = array(
        array('Id' => 'Pvp<=50', 'Value' => 'Hasta 50€'),
        array('Id' => 'Pvp>50 and Pvp<=100', 'Value' => 'Entre 50€ y 100€'),
        array('Id' => 'Pvp>100 and Pvp<=200', 'Value' => 'Entre 100€ y 200€'),
        array('Id' => 'Pvp>200 and Pvp<=500', 'Value' => 'Entre 200€ y 500€'),
        array('Id' => 'Pvp>500', 'Value' => 'Más 500€'),
    );

}

?>
