<?php

/**
 * Description of ZonaPrivadaController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 23-may-2013
 *
 */
class ZonaPrivadaController extends ControllerProject {

    protected $entity = "ZonaPrivada";
    private $errores = array();

    public function IndexAction() {

        switch ($this->request[1]) {
            case 'logout':
                return $this->logoutAction();
                break;
            case 'mis-datos':
                return $this->MisDatosAction();
                break;
            case 'mis-productos':
                return $this->MisProductosAction();
                break;
            case 'confirmacion':
                if ($this->ConfirmacionRegistro())
                    return $this->MisProductosAction();
            default:
                return parent::IndexAction();
        }
    }

    /**
     * Muestra los datos del usuario en curso
     * @return type
     */
    public function MisDatosAction() {

        $cliente = new Clientes($_SESSION['usuarioWeb']['id']);

        $this->values['registro']['IDCliente'] = $cliente->getIDCliente();
        $this->values['registro']['EMail'] = $cliente->getEMail();
        $this->values['registro']['IDTipo'] = $cliente->getIDTipo()->getPrimaryKeyValue();

        if ($cliente->getIDTipo()->getIDTipo() == '1') {
            $this->values['registro']['Par']['RazonSocial'] = $cliente->getRazonSocial();
            $this->values['registro']['Par']['Telefono'] = $cliente->getTelefono();
            $this->values['registro']['Par']['IDProvincia'] = $cliente->getIDProvincia()->getPrimaryKeyValue();
            $this->values['registro']['Par']['Observations'] = $cliente->getObservations();
        } else {
            $this->values['registro']['Pro']['RazonSocial'] = $cliente->getRazonSocial();
            $this->values['registro']['Pro']['Direccion'] = $cliente->getDireccion();
            $this->values['registro']['Pro']['CodigoPostal'] = $cliente->getCodigoPostal();
            $this->values['registro']['Pro']['Web'] = $cliente->getWeb();
            $this->values['registro']['Pro']['Telefono'] = $cliente->getTelefono();
            $this->values['registro']['Pro']['IDProvincia'] = $cliente->getIDProvincia()->getPrimaryKeyValue();
            $this->values['registro']['Pro']['Observations'] = $cliente->getObservations();
            $this->values['logo'] = $cliente->getPathNameImagenN(1);
        }

        return array(
            'template' => $this->entity . '/registro.html.twig',
            'values' => $this->values
        );
    }

    /**
     * Crear o actualizar una cuenta de usuario
     * 
     * @return type
     */
    public function RegistroAction() {

        if ($this->request['METHOD'] == 'POST') {

            $datos = $this->request['registro'];

            switch ($this->request['accion']) {
                case 'crear':
                    $cliente = new Clientes();
                    if ($this->valida()) {
                        $cliente->setEMail($datos['EMail']);
                        $cliente->setIDTipo($datos['IDTipo']);
                        $cliente->setPassword(md5($this->request['registro']['Password'] . $this->getSemilla()));
                        $cliente->setCif('.');
                        $cliente->setIDSucursal(1);
                        $cliente->setIDPais(68);
                        $cliente->setIDZona(0);
                        $cliente->setPublish(0);
                        if ($datos['IDTipo'] == '1') {
                            $cliente->setRazonSocial($datos['Par']['RazonSocial']);
                            $cliente->setIDProvincia($datos['Par']['IDProvincia']);
                            $cliente->setTelefono($datos['Par']['Telefono']);
                            $cliente->setObservations($datos['Par']['Observations']);
                            $cliente->setDireccion('.');
                        } else {
                            $cliente->setRazonSocial($datos['Pro']['RazonSocial']);
                            $cliente->setDireccion($datos['Pro']['Direccion']);
                            $cliente->setCodigoPostal($datos['Pro']['CodigoPostal']);
                            $cliente->setWeb($datos['Pro']['Web']);
                            $cliente->setIDProvincia($datos['Pro']['IDProvincia']);
                            $cliente->setTelefono($datos['Pro']['Telefono']);
                            $cliente->setObservations($datos['Pro']['Observations']);
                        }
                        $ok = $cliente->create();
                        if (!$ok)
                            $this->errores[] = "ErrorCrear";
                        if (($ok) and ($this->request['FILES']['logoEmpresa']['name']))
                            $this->subeImagen($cliente->getIDCliente(), $this->request['FILES']['logoEmpresa']);
                    }

                    $this->values['errores'] = $this->errores;

                    if (count($this->errores) == 0) {
                        $template = "{$this->entity}/confirmacionRegistro.html.twig";
                        $this->CorreoConfirmacionRegistro($cliente);
                    }
                    else
                        $template = "{$this->entity}/registro.html.twig";

                    break;

                case 'guardar':
                    $cliente = new Clientes($datos['IDCliente']);
                    if ($this->valida()) {
                        if ($this->request['registro']['Password'])
                            $cliente->setPassword(md5($this->request['registro']['Password'] . $this->getSemilla()));
                        if ($datos['IDTipo'] == '1') {
                            $cliente->setRazonSocial($datos['Par']['RazonSocial']);
                            $cliente->setIDProvincia($datos['Par']['IDProvincia']);
                            $cliente->setTelefono($datos['Par']['Telefono']);
                            $cliente->setObservations($datos['Par']['Observations']);
                            $cliente->setDireccion('sin dirección');
                        } else {
                            $cliente->setRazonSocial($datos['Pro']['RazonSocial']);
                            $cliente->setDireccion($datos['Pro']['Direccion']);
                            $cliente->setCodigoPostal($datos['Pro']['CodigoPostal']);
                            $cliente->setWeb($datos['Pro']['Web']);
                            $cliente->setIDProvincia($datos['Pro']['IDProvincia']);
                            $cliente->setTelefono($datos['Pro']['Telefono']);
                            $cliente->setObservations($datos['Pro']['Observations']);
                        }
                        $ok = $cliente->save();
                        if (!$ok)
                            $this->errores[] = "ErrorGuardar";
                        if (($ok) and ($this->request['FILES']['logoEmpresa']['name']))
                            $this->subeImagen($cliente->getIDCliente(), $this->request['FILES']['logoEmpresa']);
                    }
                    $this->values['errores'] = $this->errores;
                    $template = "{$this->entity}/registro.html.twig";
                    break;
            }


            $this->values['registro'] = $this->request['registro'];
            $this->values['logo'] = $cliente->getPathNameImagenN(1);
        }
        else
            $template = "{$this->entity}/registro.html.twig";

        return array(
            'template' => $template,
            'values' => $this->values
        );
    }

    /**
     * Sube el logo asociado al usuario
     */
    private function subeImagen($idCliente, $imagen) {

        $variables = CpanVariables::getVariables("Env", "Mod", "Clientes");

        $datos = new Clientes($idCliente);
        $columnaSlug = $variables['fieldGeneratorUrlFriendly'];
        $slug = $datos->{"get$columnaSlug"}();
        unset($datos);

        // Borrar las eventuales imagenes que existieran
        $img = new CpanDocs();
        $img->borraDocs('Clientes', $idCliente, 'image%');
        unset($img);

        foreach ($variables['images'] as $key => $value) {

            if ($value['visible'] == '1') {

                $imagen['maxWidth'] = $value['width'];
                $imagen['maxHeight'] = $value['height'];
                $imagen['modoRecortar'] = "ajustar";

                $doc = new CpanDocs();
                $doc->setEntity("Clientes");
                $doc->setIdEntity($idCliente);
                $doc->setPathName("Clientes" . $idCliente);
                $doc->setName($slug);
                $doc->setTitle($slug);
                $doc->setType('image' . $key);
                $doc->setArrayDoc($imagen);
                $doc->setIsThumbnail(0);
                $doc->setPublish($value['valorDefectoPublicar']);
                $doc->create();

                if (count($doc->getErrores())) {
                    $doc->borraDocs("Clientes", $idCliente, 'image%');
                }

                unset($doc);
            }
        }
    }

    /**
     * Logeo del usuario
     * 
     * @return type
     */
    public function LoginAction() {

        $this->values['login'] = $this->request['login'];

        $usuarios = new WebUsuarios();
        $usuario = $usuarios->find("EMail", $this->request['login']['EMail']);
        unset($usuarios);

        if ($usuario->getPrimaryKeyValue()) {
            // El usuario existe
            if ($usuario->getPassword() == md5($this->request['login']['Password'] . $this->getSemilla())) {
                $_SESSION['usuarioWeb'] = array(
                    'Id' => $usuario->getPrimaryKeyValue(),
                    'nombre' => $usuario->getRazonSocial(),
                    'IdPerfil' => $usuario->getIdPerfil()->getPrimaryKeyValue(),
                );
                $this->values['login']['error'] = 0;
            } else {
                // Password incorrecta
                $this->values['login']['error'] = 2;
            }
        } else {
            // Usuario no encontrado
            $this->values['login']['error'] = 1;
        }

        unset($usuario);

        if ($this->values['login']['error']) {
            return array(
                'template' => $this->entity . "/index.html.twig",
                'values' => $this->values,
            );
        } else {
            include "modules/Index/IndexController.class.php";
            $indexController = new IndexController($this->request);
            return $indexController->IndexAction();
        }
    }

    /**
     * Cierra la sesion del usuario y vuelve al home
     * @return type
     */
    public function LogoutAction() {
        unset($_SESSION['usuarioWeb']);
        include_once "modules/Index/IndexController.class.php";
        $index = new IndexController($this->request);

        return $index->IndexAction();
    }

    public function OlvidoAction() {

        if ($this->request['METHOD'] == 'POST') {
            if ($this->validaOlvido()) {
                $passw = new Password(6);
                $nueva = $passw->genera();
                unset($passw);
                $usuario = new WebUsuarios();
                $usuario = $usuario->find("EMail", $this->request['campos']['email']['valor']);
                $usuario->setPassword($nueva);
                $usuario->save();
                $email = $usuario->getEMail();

                $subject = "Nueva contraseña";
                $mensaje = "<p>Le ha sido generada una contrase&ntilde;a nueva de acceso al sistema.</p>" .
                        "<p>La contrase&ntilde;a nueva es: " . $nueva . "</p>";

                $mailer = new Mail($this->varWeb['Pro']['mail']);
                $plantilla = file_get_contents('docs/plantillaMailVisitante.htm');
                $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
                $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
                $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
                $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
                $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
                $plantilla = str_replace("#EMPRESA#", $this->varWeb['Pro']['globales']['empresa'], $plantilla);
                $plantilla = str_replace("#MAIL#", $this->varWeb['Pro']['globales']['from'], $plantilla);
                $plantilla = str_replace("#ASUNTO#", $subject, $plantilla);
                $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

                $envioOk = $mailer->send(
                        $email, $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], $subject, $plantilla, array()
                );
                $this->values['errores'][] = 'exitoRegenerada';
            } else {
                $this->values['campos'] = $this->request['campos'];
            }
        }

        return array(
            'template' => $this->entity . '/olvidoContrasena.html.twig',
            'values' => $this->values
        );
    }

    private function validaOlvido() {

        $campos = $this->request['campos'];
        $campos['email']['valor'] = trim($campos['email']['valor']);

        $ok = true;

        if ($campos['email']['valor'] == '') {
            $this->request['campos']['email']['error'] = 'errorEmailVacio';
            $ok = false;
        } else {
            $usuario = new WebUsuarios();
            $usuario = $usuario->find("EMail", $campos['email']['valor']);
            if (!$usuario->getId()) {
                $this->request['campos']['email']['error'] = "errorEmailInexistente";
                $ok = false;
            }
            unset($usuario);
        }

        if ($campos['leidoPolitica']['valor'] == '') {
            $this->request['campos']['leidoPolitica']['error'] = "erroNoLeidoPolitica";
            $ok = false;
        }

        return $ok;
    }

    /**
     * Muestra un listado de los productos del usuario registrado
     * 
     * @return type
     */
    public function MisProductosAction() {

        $nPagina = $this->request[2];
        $var = CpanVariables::getVariables("Web", "Mod", "Articulos");
        $this->values['filtro']['orden'] = $var['especificas'];
        $this->values['filtro']['productosPorPagina'] = $this->registrosPorPagina;

        /* PRODUCTOS PRINCIPALES */
        $this->values['productos'] = ErpArticulos::getArticulosPaginadosUsuario('', '', $nPagina, 0);

        /* INFORMACION RELACIONADA */
        $relacion = new CpanRelaciones();
        $objetoRelacionados = $relacion->getObjetosRelacionados($this->request['Entity'], $this->request['IdEntity'], "GconContenidos");
        unset($relacion);
        $this->values['informacionRelacionada'] = $objetoRelacionados;

        /* NOTICIAS - mostrar solo 2 noticias */
        $this->values['noticia'] = Noticias::getNoticias(true, 1);

        return array(
            'template' => $this->entity . '/misProductos.html.twig',
            'values' => $this->values
        );
    }

    private function ConfirmacionRegistro() {

        $ok = false;

        $cliente = new Clientes();
        $em = new EntityManager($cliente->getConectionName());
        if ($em->getDbLink()) {
            $query = "select IDCliente from {$cliente->getDataBaseName()}.{$cliente->getTableName()} where PrimaryKeyMD5='{$this->request[2]}' and Vigente='1' and Deleted='0'";
            $em->query($query);
            $rows = $em->fetchResult();
            $em->desConecta();
            $idCliente = $rows[0]['IDCliente'];
            if ($idCliente != '') {
                $cliente = new Clientes($idCliente);
                $cliente->setPublish(1);
                $ok = $cliente->save();
                if ($ok) {
                    $_SESSION['usuarioWeb'] = array(
                        'id' => $cliente->getPrimaryKeyValue(),
                        'nombre' => $cliente->getRazonSocial(),
                        'tipo' => $cliente->getIDTipo()->getPrimaryKeyValue(),
                    );
                }
            }
        }
        unset($cliente);

        return $ok;
    }

    /**
     * Envía un correo electrónico de confirmación de registro al usuario
     * y otro al webmaster
     * 
     * En el correo que se envía al usuario, se adjunto un link para que finalice
     * el proceso de registro
     * 
     * @param Clientes $cliente
     * @return boolean
     */
    private function CorreoConfirmacionRegistro(Clientes $cliente) {

        $email = $cliente->getEMail();
        $mensaje = "<p>Gracias por registrarse con nosotros.</p>";
        $mensaje .= "<p>Para finalizar el proceso haga click en este enlace <a href='http://{$this->varWeb['Pro']['globales']['dominio']}/zona-privada/confirmacion/{$cliente->getPrimaryKeyMD5()}'>{$this->varWeb['Pro']['globales']['dominio']}/zona-privada/confirmacion/{$cliente->getPrimaryKeyMD5()}</a></p>";

        $mailer = new Mail($this->varWeb['Pro']['mail']);
        $plantilla = file_get_contents('docs/plantillaMailVisitante.htm');
        $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
        $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
        $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
        $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
        $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
        $plantilla = str_replace("#EMPRESA#", $this->varWeb['Pro']['globales']['empresa'], $plantilla);
        $plantilla = str_replace("#MAIL#", $this->varWeb['Pro']['globales']['from'], $plantilla);
        $plantilla = str_replace("#ASUNTO#", "Confirmación de registro", $plantilla);
        $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

        $envioOk = $mailer->send(
                $email, $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Confirmación de registro', $plantilla, array()
        );

        if ($envioOk) {
            $mensaje = "<p>Se ha registro un usuario.</p>";
            $mensaje .= "<br />";
            $mensaje .= "<p>Nombre: {$cliente->getRazonSocial()}</p>";
            $mensaje .= "<p>{$cliente->getEMail()}</p>";
            $mensaje .= "<p>Provincia: {$cliente->getIDProvincia()->getProvincia()}</p>";
            $mensaje .= "<p>Teléfono: {$cliente->getTelefono()}</p>";
            $mensaje .= "<p>Tipo: {$cliente->getIDTipo()->getTipo()}</p>";
            $mensaje .= "<br />";
            $mensaje .= "<p>Actualmente hay {$cliente->getNumberOfRecords()} usuarios registrados en total.</p>";

            $plantilla = file_get_contents('docs/plantillaMailWebMaster.htm');
            $plantilla = str_replace("#TITLE#", $this->varWeb['Pro']['meta']['title'], $plantilla);
            $plantilla = str_replace("#DOMINIO#", $this->varWeb['Pro']['globales']['dominio'], $plantilla);
            $plantilla = str_replace("#TEXTOLOPD#", $this->varWeb['Pro']['mail']['textoLOPD'], $plantilla);
            $plantilla = str_replace("#FECHA#", date('d-m-Y'), $plantilla);
            $plantilla = str_replace("#HORA#", date('H:m:i'), $plantilla);
            $plantilla = str_replace("#VISITANTE#", $cliente->getRazonSocial(), $plantilla);
            $plantilla = str_replace("#MAIL#", $email, $plantilla);
            $plantilla = str_replace("#TELEFONO#", $cliente->getTelefono(), $plantilla);
            $plantilla = str_replace("#ASUNTO#", 'Registro de usuario', $plantilla);
            $plantilla = str_replace("#MENSAJE#", $mensaje, $plantilla);

            return $mailer->send(
                            $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from'], $this->varWeb['Pro']['mail']['from_name'], 'Registro de usuario', $plantilla, array()
            );
        }


        unset($mailer);
    }

    /**
     * Valida el registro/modificacion de usuario
     * 
     * @return type
     */
    private function valida() {

        $datos = $this->request['registro'];

        if (trim($datos['EMail']) == '')
            $this->errores[] = 'errorEmailVacio';
        elseif ($datos['IDCliente'] == '') {
            $email = trim($datos['EMail']);
            $usuario = new WebUsuarios();
            $usuario = $usuario->find("Email", $email);
            if ($usuario->getId() != 0) {
                $this->errores[] = "errorEmailExiste";
            }
            unset($usuario);
        }

        if ($datos['IDCliente'] == '') {
            // Cliente nuevo. Creación
            if (trim($datos['Password']) == '')
                $this->errores[] = "errorPasswordVacio";
            elseif ($this->request['repitePassword'] != $datos['Password'])
                $this->errores[] = "errorPasswordDiferente";
            if (!isset($datos['leidoPolitica']))
                $this->errores[] = "errorPoliticaPrivacidad";
        } else {
            // Cliente existente. Modificación
            if (trim($datos['Password']) != trim($this->request['repitePassword']))
                $this->errores[] = "errorPasswordDiferente";
        }

        if (!isset($datos['IDTipo']))
            $this->errores[] = "errorTipoUsuario";
        elseif ($datos['IDTipo'] == '1') {
            if (trim($datos['Par']['RazonSocial']) == '')
                $this->errores[] = "errorRazonSocial";
            //if (trim($datos['Par']['Telefono']) == '')
            //    $this->errores[] = "errorTelefono";
            if (trim($datos['Par']['IDProvincia']) == '')
                $this->errores[] = "errorProvincia";
        } else {
            if (trim($datos['Pro']['RazonSocial']) == '')
                $this->errores[] = "errorRazonSocial";
            if (trim($datos['Pro']['Direccion']) == '')
                $this->errores[] = "errorDireccion";
            //if (trim($datos['Pro']['Telefono']) == '')
            //    $this->errores[] = "errorTelefono";
            if (trim($datos['Pro']['IDProvincia']) == '')
                $this->errores[] = "errorProvincia";
        }

        return (count($this->errores) == 0);
    }

}

?>
