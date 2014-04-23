<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE FAMILIAS/CATEGORIAS ERP
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 12-mayo-2013
 */
class ErpFamilias {

    /**
     * Devuelve un objeto familia
     * 
     * @param integer $idFamilia El id de la familia
     * @return \Familias
     */
    static function getFamilia($idFamilia) {
        return new Familias($idFamilia);
    }

    /**
     * Devuelve un array de objetos familias
     * 
     * @param integer $idFamiliaPadre El id de la familia padre. Por defecto 0 (las categorias)
     * @param integer $nItems El número de familias a devolver. Por defecto todas
     * @return array
     */
    static function getRangoFamilias($idFamiliaPadre = 0, $posicionInicio = 0, $nItems = 0) {

        $array = array();

        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia", "BelongsTo='{$idFamiliaPadre}'", "SortOrder ASC {$limite}");
        unset($familia);

        foreach ($rows as $row)
            $array[] = self::getFamilia($row['IDFamilia']);

        return $array;
    }

    /**
     * Devuelve un array de objetos familias que son categorias y
     * están marcadas para mostrar en portada.
     * 
     * @param integer $posicionInicio A partir de que categoria
     * @param integer $nItems Número de categorias a devolver. Por defecto todas
     * @return array Array de categorias
     */
    static function getCategoriasPortada($posicionInicio = 0, $nItems = 0) {

        $array = array();

        $nItems = ($nItems <= 0) ? 999999 : $nItems;
        $limite = "LIMIT {$posicionInicio},{$nItems}";

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia", "BelongsTo='0' and MostrarPortada='1'", "OrdenPortada ASC {$limite}");
        unset($familia);

        foreach ($rows as $row)
            $array[] = self::getFamilia($row['IDFamilia']);

        return $array;
    }

    /**
     * Devuelve un array con todas las categorias
     * 
     * @return array
     */
    static function getCategorias() {

        $familia = new Familias();
        $array = $familia->cargaCondicion("IDFamilia as Id,Familia as Value", "BelongsTo='0'");
        unset($familia);

        return $array;
    }

    /**
     * Devuelve un array con familias
     * 
     * Si se indica $idCategoria, se devuelve las familias de dicha categoría.
     * Si se no indica $idCategoria, se devuelven todas las familias de
     * nivel jerárquico 2 independientemente de la categoria a la que pertenezcan
     * 
     * @param integer $idCategoria El id de la categoría. Opcional
     * @return array (Id,Value)
     */
    static function getFamilias($idCategoria = 0) {

        $familia = new Familias();

        $array = ($idCategoria > 0) ?
                $familia->cargaCondicion("IDFamilia as Id,Familia as Value", "BelongsTo='{$idCategoria}'") :
                $familia->cargaCondicion("IDFamilia as Id,Familia as Value", "NivelJerarquico='2'");

        unset($familia);

        return $array;
    }

    /**
     * Devuelve un array de objetos fabricantes que están
     * relacionados con la categoría $idCategoria.
     * 
     * Si no se indicar $idCategoria, se devuelven todos los fabricantes
     * 
     * @param integer $idCategoria El id de la categoría. Opcional
     * @return array de objetos Fabricantes
     */
    static function getFabricantes($idCategoria = 0) {

        if ($idCategoria) {
            $familia = new Familias($idCategoria);
            $array = $familia->getFabricantes();
            unset($familia);
        } else {
            $fabricantes = new Fabricantes();
            $rows = $fabricantes->cargaCondicion("IDFabricante");
            unset($fabricantes);
            foreach ($rows as $row)
                $array[] = new Fabricantes($row['IDFabricante']);
        }

        return $array;
    }

    /**
     * Devuelve un array de categorias y familias
     * 
     * @param boolean $enPortada Si es TRUE solo se muestran las categorias de portada
     * @param int $nItems Número máximo de categorías a mostrar. Por defecto todas.
     * @return array
     */
    static function getCategoriasFamilias($enPortada = true, $nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $familia = new Familias();
        $rows = $familia->cargaCondicion("IDFamilia", "BelongsTo='0' and MostrarPortada='{$enPortada}'", "OrdenPortada ASC {$limite}");
        unset($familia);

        foreach ($rows as $row) {

            $familia = new Familias();
            $rows1 = $familia->cargaCondicion("IDFamilia", "BelongsTo='{$row['IDFamilia']}'");
            unset($familia);

            $array[$row['IDFamilia']]['categoria'] = self::getFamilia($row['IDFamilia']);

            foreach ($rows1 as $row1)
                $array[$row['IDFamilia']]['familias'][] = self::getFamilia($row1['IDFamilia']);
        }

        return $array;
    }

    /**
     * Devuelve un array paginado de artículos que pertenecen
     * a la categoría, familia o subfamilia $idCategoria
     * 
     * El array tiene dos elementos:
     * 
     * - articulos => Array de objetos articulos
     * - paginacion => array paginacion
     * 
     * @param integer $idCategoria El id de la categoría, familia o subfamilia
     * @param string $orgen El criterio de orden. Por defecto "SortOrder ASC"
     * @param integer $pagina El número de página
     * @param integer $nItems Número de items por página
     * @return array Array de artículos paginado
     */
    static function getArticulosPaginados($idCategoria=0, $filtro='(1)', $orden = "SortOrder ASC", $pagina = 1, $nItems = 4) {

        $filtro .= " AND (Vigente='1')";
        
        if ($idCategoria) {
            $familia = new Familias($idCategoria);
            $nivelJerarquico = $familia->getNivelJerarquico();
            unset($familia);

            switch ($nivelJerarquico) {
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
            $filtro .= " AND ({$campo}='{$idCategoria}')";
        }

        $itemsPorPagina = ($nItems <= 0) ? 4 : $nItems;
        $nPagina = ($pagina <= 0) ? 1 : $pagina;

        Paginacion::paginar("Articulos", $filtro, $orden, $nPagina, $itemsPorPagina);

        foreach (Paginacion::getRows() as $row)
            $articulos[] = new Articulos($row['IDArticulo']);

        return array(
            'articulos' => $articulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array sin paginar de artículos que pertenecen
     * a la categoría, familia o subfamilia $idCategoria
     * 
     * @param integer $idCategoria El id de la categoría, familia o subfamilia
     * @param integer $nItems Número de items a devolver. Por defecto todos
     * @return array Array de artículos sin paginar
     */
    static function getArticulos($idCategoria, $nItems = 0) {

        $array = array();

        $familia = new Familias($idCategoria);
        $nivelJerarquico = $familia->getNivelJerarquico();
        unset($familia);

        switch ($nivelJerarquico) {
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

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";
        $filtro = "{$campo}='{$idCategoria}' and Vigente='1'";

        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("IDArticulo", $filtro, "SortOrder ASC {$limite}");
        unset($articulo);

        foreach ($rows as $row)
            $array[] = new Articulos($row['IDArticulo']);

        return $array;
    }

    /**
     * Devuelve un array de objetos articulos que están relacionados con
     * la categoria, familia o subfamilia indicada en $idCategoria
     * 
     * Esto se hace en base a la tabla CpanRelaciones.
     * 
     * Los artículos que se devuelven son aquellos que pertenecen a las
     * categorias, familias o subfamilias relacionadas con la indicada.
     * 
     * Si la indicada no tuviese relaciones, se devuelven los de la cateogría padre.
     * 
     * @param integer $idCategoria El id de la categoria, familia o subfamilia
     * @param integer $nItems El número de articulos a devolver por cada familia
     * @return array Array de objetos articulos
     */
    static function getArticulosRelacionados($idCategoria, $nItems = 5) {

        // Obtener el array de los objetos relacionadas
        $relacion = new CpanRelaciones();
        $familiasRelacionadas = $relacion->getObjetosRelacionados("Familias", $idCategoria);
        unset($relacion);

        // Si la categoria indicada no tiene relaciones, busco las
        // eventuales relaciones con la categoría padre a la indicada
        if (count($familiasRelacionadas) == 0) {
            $familia = self::getFamilia($idCategoria);
            if ($familia->getNivelJerarquico() > 1) {
                $idCategoria = $familia->getBelongsTo()->getIDFamilia();
                $relacion = new CpanRelaciones();
                $familiasRelacionadas = $relacion->getObjetosRelacionados("Familias", $idCategoria);
                unset($relacion);
            }
            unset($familia);
        }

        $nItems = ($nItems <= 0) ? 5 : $nItems;
        $array = array();

        // Para cada una de las familias relacionadas, obtengo sus $nItems artículos
        foreach ($familiasRelacionadas as $familia) {
            $arrayAux = self::getArticulos($familia->getPrimaryKeyValue(), $nItems);
            $array = array_merge($array, $arrayAux);
        }

        return $array;
    }

    /**
     * Genera el array con las categorias más visitados
     * 
     * 
     * Las categorias se ordenan descendentemente por número de visitas (NumberVisits)
     * 
     * El array tiene 2 elementos:
     * 
     * - titulo => la descripcion de la familia
     * - url => array(url => texto, targetBlank => boolean)
     * 
     * @param integer $nItems El numero máximo de elementos a devolver. Opcional. (0=todos)
     * @return array Array con las categorias/familias/subfamilias
     */
    static function getMasVisitados($nItems = 0) {

        $array = array();

        $limite = ($nItems <= 0) ? "" : "LIMIT {$nItems}";

        $categoria = new Familias();

        $rows = $categoria->cargaCondicion("IDFamilia", "", "NumberVisits DESC {$limite}");

        foreach ($rows as $row) {
            $categoria = new Familias($row['IDFamilia']);
            $array[] = array(
                'titulo' => $categoria->getFamilia(),
                'url' => $categoria->getObjetoUrlAmigable()->getHref(),
            );
        }
        unset($categoria);

        return $array;
    }

    /**
     * Devuelve un array con las propiedades y valores de la familia $idFamilia
     * 
     * El índice del array es el id de la propiedad. Cada elemento tiene:
     * 
     * - Titulo => el título de la propiedad
     * - Tipo => el tipo de propiedad (1: desplegable, 2: lista valores, 3: color)
     * - Valores => los valores posibles
     * 
     * @param int $idFamilia El id de la familia
     * @param boolean $filtrable True para obtener solo las propiedades filtrables. Por defecto todas.
     * @return array
     */
    static function getPropiedades($idFamilia, $filtrable = false) {

        $array = array();

        $filtro = "IDFamilia='{$idFamilia}'";
        $filtro .= ($filtrable) ? " AND Filtrable='1'" : "";

        $propiedades = new FamiliasPropiedades();
        $rows = $propiedades->cargaCondicion("IDPropiedad", $filtro);
        unset($propiedades);

        foreach ($rows as $row) {
            $propiedad = new Propiedades($row['IDPropiedad']);
            $array[$row['IDPropiedad']] = array(
                'Titulo' => $propiedad->getTitulo(),
                'Tipo' => $propiedad->getIDTipo()->getIDTipo(),
                'Valores' => $propiedad->getValores(),
            );
        }

        return $array;
    }

}

?>
