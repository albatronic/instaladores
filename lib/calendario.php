<?php

/**
 * GENERA EL CODIGO HTML DE UN CALENDARIO
 *
 * ESTE SCRIPT ES LLAMADO POR LAS FUNCIONES AJAX.
 * DEVUELVE UN STRING CON CODIGO HTML QUE SERÁ UTILIZADO
 * PARA REPOBLAR EL TAG HTML QUE CORRESPONDA
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informatica ALBATRONIC
 * @since 27.05.2011
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

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$tipo = $_GET['tipo'];

// PINTA EL CALENDARIO DEL MES $mes, AÑO $ano MOSTRANDO LA MARCA DE LOS DÍAS CON EVENTOS
$calendario = Calendario::showCalendario($mes,$ano,$tipo,false,3);
echo $calendario;
?>
