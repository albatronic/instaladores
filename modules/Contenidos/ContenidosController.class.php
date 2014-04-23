<?php

/**
 * Description of ContenidosController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class ContenidosController extends ControllerProject {

    protected $entity = "Contenidos";
    var $formContacta = array();

    public function IndexAction() {

        $contenido = Contenidos::getContenidoDesarrollado($this->request['IdEntity'], 12);
        $this->values['contenidoDesarrollado'] = $contenido;

        $formacionJornadas = strtoupper(trim($contenido['contenido']->getMetadata('Formacion_o_Jornadas')));
        switch ($formacionJornadas) {
            case 'F':
                $template = "FormacionYJornadas/curso.html.twig";
                break;
            case 'J':
                $template = "FormacionYJornadas/curso.html.twig";
                break;
            default:
                $template = $this->entity . "/index.html.twig";
        }

        switch ($this->request['METHOD']) {
            case 'GET':
                $this->formContacta = array(
                    'campos' => array(
                        'Nombre' => array('valor' => 'Nombre', 'error' => false),
                        'Apellidos' => array('valor' => 'Apellidos', 'error' => false),
                        'DNI' => array('valor' => 'DNI', 'error' => false),
                        'Empresa' => array('valor' => 'Empresa', 'error' => false),
                        'Email' => array('valor' => 'Email', 'error' => false),
                        'Telefono' => array('valor' => 'Telefono', 'error' => false),
                        'Asunto' => array('valor' => 'Asunto', 'error' => false),
                        'Comentarios' => array('valor' => 'Comentarios', 'error' => false),
                    ),
                );
                $this->values['entity'] = "GconContenidos";
                $this->values['idEntity'] = $this->request['IdEntity'];
                $this->values['formContacta'] = $this->formContacta;
                break;

            case 'POST':
                $this->formContacta = $this->request['campos'];

                if ($this->Valida()) {
                    if ((file_exists('docs/plantillaMailVisitante.htm')) and (file_exists('docs/plantillaMailWebMaster.htm'))) {

                        $mailer = new Mail($this->varWeb['Pro']['mail']);
                        $envioOk = $this->enviaVisitante($mailer, 'docs/plantillaMailVisitante.htm');

                        if ($envioOk)
                            $envioOk = $this->enviaWebMaster($mailer, 'docs/plantillaMailWebMaster.htm');

                        $this->formContacta['accion'] = 'envio';
                        $this->formContacta['resultado'] = $envioOk;
                        $this->formContacta['mensaje'] = ($envioOk) ?
                                $this->varWeb['Pro']['mail']['mensajeExito'] :
                                $this->varWeb['Pro']['mail']['mensajeError'];

                        unset($mailer);
                    } else {
                        $this->formContacta['accion'] = 'envio';
                        $this->formContacta['resultado'] = false;
                        $this->formContacta['mensaje'] = "No se han definido las plantillas.";
                    }
                }
                $this->values['entity'] = $this->request['entity'];
                $this->values['idEntity'] = $this->request['idEntity'];
                $this->values['formContacta'] = $this->formContacta;
                break;
        }


        return array(
            'template' => $template,
            'values' => $this->values,
        );
    }

    /**
     * Envía el correo de confirmación al visitante
     * en base a la plantilla htm $ficheroPlantilla.
     * 
     * @param Mail $mailer objeto mailer
     * @param string $ficheroPlantilla El archivo que tiene la plantilla htm a enviar
     * @return boolean TRUE si se envío con éxito
     */
    private function enviaVisitante($mailer, $ficheroPlantilla) {
        $mensaje  = "<p>Confirmación de la solicitud de inscripción a:</p>{$this->values['contenidoDesarrollado']['contenido']->getTitulo()}<br/><br/>";
        $mensaje .= $this->varWeb['Pro']['mail']['mensajeConfirmacion'];
        
        $plantilla = file_get_contents($ficheroPlantilla);
        $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
        $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
        $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
        $plantilla = str_replace("#EMPRESA#", $this->varWeb['Pro']['globales']['empresa'], $plantilla);
        $plantilla = str_replace("#MAIL#", $this->varWeb['Pro']['globales']['from'], $plantilla);
        $plantilla = str_replace("#ASUNTO#", 'Hemos recibido su solicitud de inscripción', $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        return $mailer->send(
                        $this->formContacta['campos']['Email']['valor'], $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Hemos recibido su mensaje', $plantilla, array()
        );
    }

    /**
     * Envía el correo de confirmación al webmaster
     * en base a la plantilla htm $ficheroPlantilla.
     * 
     * @param Mail $mailer objeto mailer
     * @param string $ficheroPlantilla El archivo que tiene la plantilla htm a enviar
     * @return boolean TRUE si se envío con éxito
     */
    private function enviaWebMaster($mailer, $ficheroPlantilla) {

        $mensaje  = "<p>La empresa/persona abajo indicada está interesada en inscribirse en:</p>";
        $mensaje .= "{$this->values['contenidoDesarrollado']['contenido']->getTitulo()}<br/><br/>";
        $mensaje .= "<p>Nombre y apellidos:</p>{$this->formContacta['campos']['Nombre']['valor']} {$this->formContacta['campos']['Apellidos']['valor']}<br/>";
        $mensaje .= "<p>Empresa:</p>{$this->formContacta['campos']['Empresa']['valor']}<br/>";
        $mensaje .= "<p>DNI:</p>{$this->formContacta['campos']['DNI']['valor']}<br/>";
        $mensaje .= "<p>Teléfono:</p>{$this->formContacta['campos']['Telefono']['valor']}<br/>";
        $mensaje .= "<p>Email</p>{$this->formContacta['campos']['Email']['valor']}<br/>";
        $mensaje .= "<p>Comentarios:</p>{$this->formContacta['campos']['Comentarios']['valor']}<br/>";

        $plantilla = file_get_contents($ficheroPlantilla);
        $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
        $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
        $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
        $plantilla = str_replace("#VISITANTE#", $this->formContacta['campos']['Nombre']['valor'], $plantilla);
        $plantilla = str_replace("#MAIL#", $this->formContacta['campos']['Email']['valor'], $plantilla);
        $plantilla = str_replace("#TELEFONO#", $this->formContacta['campos']['Telefono']['valor'], $plantilla);
        $plantilla = str_replace("#ASUNTO#", "Solicitud Inscripción", $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        return $mailer->send(
                        $this->varWeb['Pro']['mail']['from'], $this->formContacta['campos']['Email']['valor'], $this->formContacta['campos']['nombre']['valor'], 'Ha recibido un mensaje en la web', $plantilla, array()
        );
    }

    private function Valida() {

        $error = false;

        if (!isset($this->formContacta['leidoPolitica']['valor']))
            $this->formContacta['leidoPolitica']['valor'] = '';

        foreach ($this->formContacta as $campo => $valor) {
            $valor = trim(str_replace($campo, "", $valor['valor']));
            $errorCampo = ($valor == '');
            $this->formContacta['campos'][$campo]['valor'] = ($errorCampo) ? $campo : $valor;
            $this->formContacta['campos'][$campo]['error'] = $errorCampo;
            $error = ($error or $errorCampo);
        }

        // Comprobar la validez ortográfica de la dirección de correo
        $mail = new Mail($this->varWeb['Pro']['mail']);
        if (!$mail->compruebaEmail($this->formContacta['campos']['Email']['valor'])) {
            $this->formContacta['campos']['Email']['error'] = 1;
            $error = true;
        }

        return !$error;
    }

}

?>
