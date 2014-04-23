<?php
/**
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL
 * @date 17.10.2012 14:24:31
 */

/**
 * @orm:Entity(CommProvincias)
 */
class CommProvinciasEntity extends EntityComunes {
	/**
	 * @orm GeneratedValue
	 * @orm Id
	 * @var integer
	 * @assert NotBlank(groups="CommProvincias")
	 */
	protected $Id;
	/**
	 * @var entities\CommPaises
	 * @assert NotBlank(groups="CommProvincias")
	 */
	protected $IdPais = '0';
	/**
	 * @var string
	 * @assert NotBlank(groups="CommProvincias")
	 */
	protected $Codigo;
	/**
	 * @var string
	 * @assert NotBlank(groups="CommProvincias")
	 */
	protected $Provincia = '';
	/**
	 * Nombre de la conexion a la BD
	 * @var string
	 */
	protected $_conectionName = '';
	/**
	 * Nombre de la tabla física
	 * @var string
	 */
	protected $_tableName = 'CommProvincias';
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
			array('SourceColumn' => 'Id', 'ParentEntity' => 'CommMunicipios', 'ParentColumn' => 'IdProvincia'),
			array('SourceColumn' => 'Id', 'ParentEntity' => 'CommBancosOficinas', 'ParentColumn' => 'IdProvincia'),
			array('SourceColumn' => 'Id', 'ParentEntity' => 'PcaeEmpresas', 'ParentColumn' => 'IdProvincia'),
			array('SourceColumn' => 'Id', 'ParentEntity' => 'PcaeUsuarios', 'ParentColumn' => 'IdProvincia'),
		);
	/**
	 * Relacion de entidades de las que esta depende
	 * @var string
	 */
	protected $_childEntities = array(
			'CommPaises',
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

	public function setCodigo($Codigo){
		$this->Codigo = trim($Codigo);
	}
	public function getCodigo(){
		return $this->Codigo;
	}

	public function setProvincia($Provincia){
		$this->Provincia = trim($Provincia);
	}
	public function getProvincia(){
		return $this->Provincia;
	}

} // END class CommProvincias

?>