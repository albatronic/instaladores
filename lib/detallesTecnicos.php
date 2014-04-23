<?php

/**
 * GENERA EL CODIGO HTML DE LOS DETALLES TÉCNICOS
 *
 * ESTE SCRIPT ES LLAMADO POR LAS FUNCIONES AJAX.
 * DEVUELVE UN STRING CON CODIGO HTML QUE SERÁ UTILIZADO
 * PARA REPOBLAR EL TAG HTML QUE CORRESPONDA
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 3.06.2013
 */
session_start();

if (!file_exists('../config/config.yml')) {
    echo "NO EXISTE EL FICHERO DE CONFIGURACION";
    exit;
}

if (file_exists("../bin/yaml/lib/sfYaml.php")) {
    include "../bin/yaml/lib/sfYaml.php";
} else {
    echo "NO EXISTE LA CLASE PARA LEER ARCHIVOS YAML";
    exit;
}

// ---------------------------------------------------------------
// CARGO LOS PARAMETROS DE CONFIGURACION.
// ---------------------------------------------------------------
$config = sfYaml::load('../config/config.yml');
$app = $config['config']['app'];

// ---------------------------------------------------------------
// ACTIVAR EL AUTOLOADER DE CLASES Y FICHEROS A INCLUIR
// ---------------------------------------------------------------
define(APP_PATH, $_SERVER['DOCUMENT_ROOT'] . $app['path'] . "/");
include_once "../" . $app['framework'] . "Autoloader.class.php";
Autoloader::setCacheFilePath(APP_PATH . 'tmp/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
    '../' . $app['framework'],
    '../entities/',
    '../lib/',
));
spl_autoload_register(array('Autoloader', 'loadClass'));

$idArticulo = $_GET['idArticulo'];
$idCategoria = $_GET['idCategoria'];

$html = "";

/**
if ($idArticulo) {
    $articulo = new Articulos($idArticulo);
    $propiedades = $articulo->getPropiedades();
    unset($articulo);
} else {**/
    $categoria = new Familias($idCategoria);
    $propiedades = $categoria->getPropiedades(false);
    unset($categoria);
//}

foreach ($propiedades as $propiedad) {
    $html .= "<article class='cont90 marginRight30'><label>{$propiedad['Titulo']}</label>";
    $html .= "<select name='propiedades[".$propiedad['Id']."]' class='select celda90'>";
    $html .= "<option value='0'>:: Indique un valor</option>";    
    foreach ($propiedad['Valores'] as $valor) {
        $html .= "<option value='{$valor['Id']}'";
        if ($valor['Id'] == $propiedad['IDValor'])
            $html .= " selected";
        $html .= ">{$valor['Valor']}</option>";
    }
    $html .= "</select></article>";
}
echo $html;
?>
