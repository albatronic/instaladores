<?php

/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @since 20.06.2011 01:42:40
 */

/**
 * @orm:Entity(sucursales)
 */
class SucursalesEntity extends EntityComunes {

    /**
     * @orm:GeneratedValue
     * @orm:IDSucursal
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $IDSucursal;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $Nombre = '';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $Direccion;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $IDPais = '68';
    /**
     * @orm:Column(type="")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $IDProvincia = '18';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $IDPoblacion = '0';    
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $CodigoPostal = '0000000000';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $Telefono;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $Fax;
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $EMail;
    /**
     * @orm:Column(type="integer")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $IDResponsable = '0';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $CtaContableClientes = '430';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $CtaContableVentas = '';
    /**
     * @orm:Column(type="string")
     * @assert:NotBlank(groups="sucursales")
     */
    protected $CtaContableCompras = '';
    /**
     * Nombre de la conexion a la DB
     * @var string
     */
    protected $_conectionName = '';
    /**
     * Nombre de la tabla física
     * @var string
     */
    protected $_tableName = 'ErpSucursales';
    /**
     * Nombre de la primaryKey
     * @var string
     */
    protected $_primaryKeyName = 'IDSucursal';
    /**
     * Relacion de entidades que dependen de esta
     * @var array
     */
    protected $_parentEntities = array(
        array('SourceColumn' => 'IDSucursal', 'ParentEntity' => 'Clientes', 'ParentColumn' => 'IDSucursal'),
        array('SourceColumn' => 'IDSucursal', 'ParentEntity' => 'Articulos', 'ParentColumn' => 'IDSucursal'),
        array('SourceColumn' => 'IDSucursal', 'ParentEntity' => 'AlbaranesCab', 'ParentColumn' => 'IDSucursal'),
        array('SourceColumn' => 'IDSucursal', 'ParentEntity' => 'PstoCab', 'ParentColumn' => 'IDSucursal'),
        array('SourceColumn' => 'IDSucursal', 'ParentEntity' => 'FemitidasCab', 'ParentColumn' => 'IDSucursal'),
    );

    /**
     * GETTERS Y SETTERS
     */
    public function setIDSucursal($IDSucursal) {
        $this->IDSucursal = $IDSucursal;
    }

    public function getIDSucursal() {
        return $this->IDSucursal;
    }

    public function setNombre($Nombre) {
        $this->Nombre = trim($Nombre);
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function setDireccion($Direccion) {
        $this->Direccion = trim($Direccion);
    }

    public function getDireccion() {
        return $this->Direccion;
    }

    public function setIDPais($IDPais) {
        $this->IDPais = $IDPais;
    }

    public function getIDPais() {
        if (!($this->IDPais instanceof Paises))
            $this->IDPais = new Paises($this->IDPais);
        return $this->IDPais;
    }

    public function setIDProvincia($IDProvincia) {
        $this->IDProvincia = $IDProvincia;
    }

    public function getIDProvincia() {
        if (!($this->IDProvincia instanceof Provincias))
            $this->IDProvincia = new Provincias($this->IDProvincia);
        return $this->IDProvincia;
    }
    
    public function setIDPoblacion($IDPoblacion) {
        $this->IDPoblacion = $IDPoblacion;
    }

    public function getIDPoblacion() {
        if (!($this->IDPoblacion instanceof Municipios))
            $this->IDPoblacion = new Municipios($this->IDPoblacion);
        return $this->IDPoblacion;
    }

    public function setCodigoPostal($CodigoPostal) {
        $this->CodigoPostal = trim($CodigoPostal);
    }

    public function getCodigoPostal() {
        return $this->CodigoPostal;
    }

    public function setTelefono($Telefono) {
        $this->Telefono = trim($Telefono);
    }

    public function getTelefono() {
        return $this->Telefono;
    }

    public function setFax($Fax) {
        $this->Fax = trim($Fax);
    }

    public function getFax() {
        return $this->Fax;
    }

    public function setEMail($EMail) {
        $this->EMail = trim($EMail);
    }

    public function getEMail() {
        return $this->EMail;
    }

    public function setIDResponsable($IDResponsable) {
        $this->IDResponsable = $IDResponsable;
    }

    public function getIDResponsable() {
        if (!($this->IDResponsable instanceof Agentes))
            $this->IDResponsable = new Agentes($this->IDResponsable);
        return $this->IDResponsable;
    }

    public function setCtaContableClientes($CtaContableClientes) {
        $this->CtaContableClientes = trim($CtaContableClientes);
    }

    public function getCtaContableClientes() {
        return $this->CtaContableClientes;
    }
    
    public function setCtaContableVentas($CtaContableVentas) {
        $this->CtaContableVentas = trim($CtaContableVentas);
    }

    public function getCtaContableVentas() {
        return $this->CtaContableVentas;
    }

    public function setCtaContableCompras($CtaContableCompras) {
        $this->CtaContableCompras = trim($CtaContableCompras);
    }

    public function getCtaContableCompras() {
        return $this->CtaContableCompras;
    }
}

// END class sucursales
?>