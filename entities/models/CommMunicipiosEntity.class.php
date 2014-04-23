<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 17.10.2012 14:24:31
 */

/**
 * @orm:Entity(CommMunicipios)
 */
class CommMunicipiosEntity extends EntityComunes {
	/**
	 * @orm GeneratedValue
	 * @orm Id
	 * @var integer
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $Id;
	/**
	 * @var entities\CommPaises
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $IdPais = '0';
	/**
	 * @var entities\CommProvincias
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $IdProvincia = '0';
	/**
	 * @var string
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $Codigo;
	/**
	 * @var string
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $DigitoControl;
	/**
	 * @var string
	 * @assert NotBlank(groups="CommMunicipios")
	 */
	protected $Municipio = '';
	/**
	 * Nombre de la conexion a la BD
	 * @var string
	 */
	protected $_conectionName = '';
	/**
	 * Nombre de la tabla física
	 * @var string
	 */
	protected $_tableName = 'CommMunicipios';
	/**
	 * Nombre de la PrimaryKey
	 * @var string
	 */
	protected $_primaryKeyName = 'Id';
	/**
	 * Relacion de entidades que dependen de esta
	 * @var string
	 */
	protected $_parentEntities = array(
			array('SourceColumn' => 'Id', 'ParentEntity' => 'PcaeEmpresas', 'ParentColumn' => 'IdMunicipio'),
			array('SourceColumn' => 'Id', 'ParentEntity' => 'PcaeUsuarios', 'ParentColumn' => 'IdMunicipio'),
		);
	/**
	 * Relacion de entidades de las que esta depende
	 * @var string
	 */
	protected $_childEntities = array(
			'CommPaises',
			'CommProvincias',
			'ValoresSN',
			'ValoresPrivacy',
			'ValoresDchaIzq',
			'ValoresChangeFreq',
		);
	/**
	 * GETTERS Y SETTERS
	 */
	public function setId($Id){
		$this->Id = $Id;
	}
	public function getId(){
		return $this->Id;
	}

	public function setIdPais($IdPais){
		$this->IdPais = $IdPais;
	}
	public function getIdPais(){
		if (!($this->IdPais instanceof CommPaises))
			$this->IdPais = new CommPaises($this->IdPais);
		return $this->IdPais;
	}

	public function setIdProvincia($IdProvincia){
		$this->IdProvincia = $IdProvincia;
	}
	public function getIdProvincia(){
		if (!($this->IdProvincia instanceof CommProvincias))
			$this->IdProvincia = new CommProvincias($this->IdProvincia);
		return $this->IdProvincia;
	}

	public function setCodigo($Codigo){
		$this->Codigo = trim($Codigo);
	}
	public function getCodigo(){
		return $this->Codigo;
	}

	public function setDigitoControl($DigitoControl){
		$this->DigitoControl = trim($DigitoControl);
	}
	public function getDigitoControl(){
		return $this->DigitoControl;
	}

	public function setMunicipio($Municipio){
		$this->Municipio = trim($Municipio);
	}
	public function getMunicipio(){
		return $this->Municipio;
	}

} // END class CommMunicipios

?>