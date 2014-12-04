<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author Administrador
 */
class Error404Controller extends ControllerProject {

    var $entity = "Error404";

    public function IndexAction() {
        header("HTTP/1.0 404 Not Found");
        return parent::IndexAction();
    }

}
