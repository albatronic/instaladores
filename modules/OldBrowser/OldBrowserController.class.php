<?php
/**
 * Description of OldBrowserController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC
 * @date 21-AGOSTO-2013
 */
class OldBrowserController extends ControllerProject {

    var $entity = "OldBrowser";
    
    public function IndexAction() {
        
        $this->values['datos']['logo'] = $this->varWeb['Pro']['globales']['logoOldBrowser'];
        $this->values['datos']['empresa'] = $this->varWeb['Pro']['globales']['empresa'];
        
        return parent::IndexAction();
    }
}

?>
