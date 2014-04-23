<?php

/**
 * Description of Familias
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class Familias extends FamiliasEntity {

    public function __toString() {
        return $this->getFamilia();
    }

    public function fetchAll($column = '', $default = true, $perteneceA = 0) {
        if ($column == '')
            $column = "Familia";

        $filtro = ($perteneceA < 0) ? "" : " and BelongsTo='{$perteneceA}'";

        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "SELECT " . $this->getPrimaryKeyName() . " as Id, $column as Value FROM `{$this->_dataBaseName}`.`{$this->_tableName}` WHERE (Deleted = '0') {$filtro} ORDER BY $column ASC";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->setStatus($this->_em->numRows());
            $this->_em->desConecta();
            unset($this->_em);
        }

        if ($default == TRUE) {
            $rows[] = array('Id' => '', Value => ':: Indique un Valor');
            sort($rows);
        }

        return $rows;
    }

    /**
     * Guardo la familia y actualizo sus articulos
     * haciendo que hereden ciertas características.
     * 
     * @return boolean
     */
    public function save() {

        $ok = parent::save();

        if ($ok) {
            $this->conecta();

            $articulo = new Articulos();
            $dbName = $articulo->getDataBaseName();
            $tableName = $articulo->getTableName();
            unset($articulo);

            if (is_resource($this->_dbLink)) {
                $query = "UPDATE {$dbName}.{$tableName} set
                            Inventario='{$this->Inventario}',
                            Trazabilidad='{$this->Trazabilidad}',
                            Publish='{$this->Publish}',
                            BajoPedido='{$this->BajoPedido}',
                            BloqueoStock='{$this->BloqueoStock}',
                            ActiveFrom = CASE
                              WHEN ActiveFrom<'{$this->ActiveFrom}' THEN '{$this->ActiveFrom}' ELSE ActiveFrom
                            END,
                            ActiveTo = CASE
                              WHEN ActiveTo>'{$this->ActiveTo}' THEN '{$this->ActiveTo}' ELSE ActiveTo
                            END
                            where (IDCategoria='{$this->IDFamilia}') 
                                or (IDFamilia='{$this->IDFamilia}') 
                                or (IDSubfamilia='{$this->IDFamilia}');";
                $this->_em->query($query);
                $this->_em->desConecta();
            }
            unset($this->_em);
        }

        return $ok;
    }

    /**
     * Devuelve un array con los articulos correspondientes
     * a la familia indicada, o en su defecto a la familia actual.
     *
     * Si se indica $entidadRelacionada e $idEntidadRelacionada, se añade un elmento más que indica
     * si cada articulo está relacionado con $entidadRelacionada e $idEntidadRelacionada
     *
     * El array tiene los siguientes elementos:
     * 
     * - Id: El id del articulo
     * - Value: La descripcion del articulo
     * - PrimaryKeyMD5: la primarykey MD5
     * - Publish: TRUE/FALSE
     * - estaRelacionado: El id de la eventual relacion
     * 
     * @param integer $idFamilia El id de la familia
     * @param string $idEntidadRelacionada La entidad con la que existe una posible relación
     * @param integer $idEntidadRelacionada El id de entidad con la que existe una posible relación
     * @return array Array Id, Value de articulos
     */
    public function getArticulos($idFamilia = '', $entidadRelacionada = '', $idEntidadRelacionada = '') {

        if ($idFamilia == '')
            $idFamilia = $this->Id;

        $articulo = new ErpArticulos();
        $articulos = $articulo->cargaCondicion('IDArticulo as Id,Descripcion as Value,PrimaryKeyMD5,Publish', "IDFamilia='{$idFamilia}'", "SortOrder ASC");
        unset($articulo);

        if ($entidadRelacionada) {
            foreach ($articulos as $key => $articulo) {
                $relacion = new CpanRelaciones();
                $articulos[$key]['estaRelacionado'] = $relacion->getIdRelacion('ErpArticulos', $articulo['Id'], $entidadRelacionada, $idEntidadRelacionada);
            }
            unset($relacion);
        }
        return $articulos;
    }

    /**
     * Devuelve un array con el árbol de secciones y contenidos
     * 
     * Si se indica valor para el parámetro $idContenidoRelacionado, en el array
     * de contenidos se incluirá un elemento booleano que indica si cada contenido
     * está relacionado con el contenido cuyo valor es el parámetro.
     * 
     * El índice del array contiene el valor de la primaryKeyMD5 de cada sección y la estructura es:
     * 
     * - id => el id de la seccion
     * - titulo => el titulo de la seccion
     * - nivelJerarquico => el nivel jerárquico dentro del árbol de secciones
     * - publish => Publicar TRUE/FALSE
     * - belongsTo => El id del padre al que pertenece
     * - nHijos => El número de secciones hijas
     * - hijos => array de secciones hijas (belongsTo) con la misma estructura
     * - nArticulos => el número de artículos que posee la familia
     * - articulos => array de artículos de la familia
     * 
     * @param boolean $conArticulos
     * @param string $entidadRelacionada
     * @param integer $idEntidadRelacionada 
     * @return array Array de familias y articulos
     */
    public function getArbolHijos($conArticulos = false, $entidadRelacionada = '', $idEntidadRelacionada = '') {

        $arbol = array();

        $objeto = new $this();
        $rows = $objeto->cargaCondicion("IDFamilia as Id,PrimaryKeyMD5,NivelJerarquico,Publish,BelongsTo", "BelongsTo='0'", "SortOrder ASC");
        unset($objeto);

        foreach ($rows as $row) {
            $objeto = new $this($row['Id']);
            $arrayArticulos = ($conArticulos) ? $this->getArticulos($row['Id'], $entidadRelacionada, $idEntidadRelacionada) : array();
            $arrayHijos = $objeto->getHijos('', $conArticulos, $entidadRelacionada, $idEntidadRelacionada);

            $arbol[$row['PrimaryKeyMD5']] = array(
                'id' => $row['Id'],
                'titulo' => $objeto->__toString(),
                'nivelJerarquico' => $row['NivelJerarquico'],
                'publish' => $row['Publish'],
                'belongsTo' => $row['BelongsTo'],
                'nHijos' => count($arrayHijos),
                'hijos' => $arrayHijos,
                'nArticulos' => count($arrayArticulos),
                'articulos' => $arrayArticulos,
            );
            if ($entidadRelacionada) {
                $relacion = new CpanRelaciones();
                $arbol[$row['PrimaryKeyMD5']]['estaRelacionado'] = $relacion->getIdRelacion($entidadRelacionada, $idEntidadRelacionada, 'ErpFamilias', $row['Id']);
            }
        }

        unset($objeto);
        return $arbol;
    }

    /**
     * Genera el árbol genealógico con las entidades hijas de la
     * entidad $idPadre.
     * 
     * El árbol se genera de forma recursiva sin límite de profundidad.
     * 
     * El array lleva valor únicamente en el índice, y dicho valor es el
     * id de las entidades.
     * 
     * @param integer $idPadre El id de la entidad padre
     * @return array
     */
    public function getHijos($idPadre = '', $conArticulos = FALSE, $entidadRelacionada = '', $idEntidadRelacionada = '') {

        if ($idPadre == '')
            $idPadre = $this->getPrimaryKeyValue();

        $this->getChildrens($idPadre, $conArticulos, $entidadRelacionada, $idEntidadRelacionada);
        return $this->_hijos[$idPadre];
    }

    /**
     * Generar un árbol genealógico con las entidades hijas
     * de la entidad cuyo id es $idPadre
     *
     * @param integer $idPadre El id de la entidad padre
     * @return array Array con los objetos hijos
     */
    private function getChildrens($idPadre, $conArticulos, $entidadRelacionada, $idEntidadRelacionada) {

        // Obtener todos los hijos del padre actual
        $hijos = $this->cargaCondicion('IDFamilia as Id,PrimaryKeyMD5,NivelJerarquico,Publish,BelongsTo', "BelongsTo='{$idPadre}'", "SortOrder ASC");

        foreach ($hijos as $hijo) {
            $aux = new $this($hijo['Id']);
            $arrayArticulos = ($conArticulos) ? $this->getArticulos($hijo['Id']) : array();
            $arrayHijos = $this->getChildrens($hijo['Id'], $conArticulos, $entidadRelacionada, $idEntidadRelacionada);
            $this->_hijos[$idPadre][$hijo['PrimaryKeyMD5']] = array(
                'id' => $hijo['Id'],
                'titulo' => $aux->__toString(),
                'nivelJerarquico' => $hijo['NivelJerarquico'],
                'publish' => $hijo['Publish'],
                'belongsTo' => $hijo['BelongsTo'],
                'nHijos' => count($arrayHijos),
                'hijos' => $arrayHijos,
                'nArticulos' => count($arrayArticulos),
                'articulos' => $arrayArticulos,
            );
            if ($entidadRelacionada) {
                $relacion = new CpanRelaciones();
                $this->_hijos[$idPadre][$hijo['PrimaryKeyMD5']]['estaRelacionado'] = $relacion->getIdRelacion($entidadRelacionada, $idEntidadRelacionada, 'ErpFamilias', $hijo['Id']);
            }
            unset($hijo);
        }

        return $this->_hijos[$idPadre];
    }

    /**
     * Devuelve un array con las propiedades asignadas
     * a la familia en curso.
     * 
     * Si el parámetro $todas es false se incluyen todas las propiedades pero
     * con una marca a false para las propiedades que no están asignadas.
     * 
     * Si el parámetro $todas es true se incluyen solo las propiedades asignadas
     * y con la marca a true
     * 
     * El indice el array es el id de la propiedad y tiene dos elementos:
     * 
     * - Titulo: el titulo de la propiedad
     * - Asignada: true o false
     * - Filtrable: true o false
     * - Valores: array de valores (ID,Value)
     * 
     * @param boolean $todas
     * @return array
     */
    public function getPropiedades($todas = false) {

        $propiedades = array();

        $propiedad = new Propiedades();
        // Cojo todas las propiedades
        $aux = $propiedad->fetchAll('Titulo', false);
        foreach ($aux as $item)
            $propiedadesTodas[$item['Id']] = $item['Value'];

        // Cojo las que están asignadas a la familia
        $familiaPropiedad = new FamiliasPropiedades();
        $rows = $familiaPropiedad->cargaCondicion("IDPropiedad, Filtrable", "IDFamilia='{$this->IDFamilia}'");

        $valores = new PropiedadesValores();
        foreach ($rows as $row)
            $propiedades[$row['IDPropiedad']] = array(
                'Id' => $row['IDPropiedad'],
                'Titulo' => $propiedadesTodas[$row['IDPropiedad']],
                'Asignada' => true,
                'Filtrable' => $row['Filtrable'],
                'Valores' => $valores->getValores($row['IDPropiedad']),
            );


        if ($todas)
            foreach ($propiedadesTodas as $key => $titulo)
                if (!isset($propiedades[$key]))
                    $propiedades[$key] = array(
                        'Id' => $key,
                        'Titulo' => $propiedadesTodas[$key],
                        'Asignada' => false,
                        'Valores' => $valores->getValores($key),
                    );

        return $propiedades;
    }

    /**
     * Devuelve true o false según la familia tenga o no propiedades asociadas
     * 
     * @return boolean True si la familia tiene propiedades
     */
    public function TienePropiedades() {

        $propiedad = new FamiliasPropiedades();
        $rows = $propiedad->cargaCondicion("Id", "IDFamilia='{$this->getPrimaryKeyValue()}'");
        unset($propiedad);

        return(count($rows) > 0);
    }

    /**
     * Devuelve el número de artículos de la categoria/familia/subfamilia en curso
     * 
     * No tiene en cuenta los no vigentes
     * 
     * @return integer El número de artículos
     */
    public function getNArticulos() {

        switch ($this->NivelJerarquico) {
            case 1:
                $campo = "IDCategoria";
                break;
            case 2:
                $campo = "IDFamilia";
                break;
            case 3:
                $campo = "IDSubfamilia";
                break;
        }

        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("count(IDArticulo) as nArticulos", "{$campo}='{$this->IDFamilia}' and Vigente='1'");
        unset($articulo);

        return $rows[0]['nArticulos'];
    }

    /**
     * Devuelve un array de objetos fabricantes que están relacionados
     * con la categoría/famila/subfamilia en curso
     * 
     * @return \Fabricantes
     */
    public function getFabricantes() {

        switch ($this->NivelJerarquico) {
            case 1:
                $campo = "IDCategoria";
                break;
            case 2:
                $campo = "IDFamilia";
                break;
            case 3:
                $campo = "IDSubfamilia";
                break;
        }

        $array = array();

        $filtro = "{$campo}='{$this->IDFamilia}' and Vigente='1'";

        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("distinct IDFabricante", $filtro);
        unset($articulo);

        foreach ($rows as $row)
            $array[] = new Fabricantes($row['IDFabricante']);

        return $array;
    }

}

?>
