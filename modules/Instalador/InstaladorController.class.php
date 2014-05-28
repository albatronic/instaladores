<?php

/**
 * Description of InstaladorController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática Albatronic, SL
 * @date 06-nov-2012
 *
 */
class InstaladorController extends ControllerProject {

    protected $entity = "Instalador";

    public function __construct($request) {

        parent::__construct($request);

        $municipio = new CommMunicipios();
        $usuario = new WebUsuarios();

        $this->values['municipios'] = $municipio->cargaCondicion("Id,Municipio", "Id in (select distinct(IdMunicipio) from WebUsuarios where Publish='1')", "Municipio ASC");
        $this->values['codigosPostales'] = $usuario->cargaCondicion("distinct(CodigoPostal) as Id","","CodigoPostal ASC");

        unset($municipio);
        unset($usuario);
    }

    /**
     * Saca un listado de instaladores electrecistas y fontaneros
     * en base al filtro municipio o codigo postal
     * 
     * @return array
     */
    public function IndexAction() {

        if ($this->request['idMunicipio'] != '') {
            $filtro = "IdMunicipio='{$this->request['idMunicipio']}'";
        } else {
            $filtro = "CodigoPostal='{$this->request['codigoPostal']}'";
        }

        $usuario = new WebUsuarios();

        $rows = $usuario->cargaCondicion("Id", "IdPerfil='2' and {$filtro}", "Apellidos ASC");
        $eletricistas = array();
        foreach ($rows as $row)
            $eletricistas[] = new WebUsuarios($row['Id']);
        shuffle($eletricistas);
        
        $rows = $usuario->cargaCondicion("Id", "IdPerfil='3' and {$filtro}", "Apellidos ASC");
        $fontaneros = array();
        foreach ($rows as $row)
            $fontaneros[] = new WebUsuarios($row['Id']);
        shuffle($fontaneros);
        
        unset($usuario);
        
        $this->values['electricistas'] = $eletricistas;
        $this->values['fontaneros'] = $fontaneros;
        $this->values['idMunicipio'] = $this->request['idMunicipio'];
        $this->values['codigoPostal'] = $this->request['codigoPostal'];


        return parent::IndexAction();
    }

    /**
     * Muestra la ficha completa de un instalador
     * @return array
     */
    public function FichaAction() {

        $instalador = new WebUsuarios($this->request['IdEntity']);

        $relacion = new CpanRelaciones();
        $servicios = $relacion->getObjetosRelacionados("WebUsuarios", $this->request['IdEntity'], "ServServicios");
        unset($relacion);

        $this->values['instalador'] = $instalador;
        $this->values['servicios'] = $servicios;

        return array(
            'values' => $this->values,
            'template' => $this->entity . "/ficha.html.twig",
        );
    }

}

?>
