<?php

/**
 * CLASE ESTÁTICA PARA LA GESTION DE ARTICULOS ERP
 *
 *
 * @author Sergio Pérez
 * @copyright Informática Albatronic, SL
 * @version 1.0 12-mayo-2013
 */
class ErpArticulos {

    /**
     * Devuelve un objeto artículo
     * @param integer $idArticulo El id del articulo
     * @return \Articulos
     */
    static function getArticulo($idArticulo) {
        return new Articulos($idArticulo);
    }

    /**
     * Devuelve un array en el detalle técnico del artículo $idArticulo
     * 
     * El array tiene tantos items como propiedades técnicas
     * y cada item tiene dos elementos:
     * 
     * - titulo: el título de la propiedad
     * - valor: el valor de la propiedad
     * 
     * @param int $idArticulo El id del artículo
     * @return array
     */
    static function getDetalleTecnico($idArticulo) {

        $array = array();

        $detalle = new ArticulosPropiedades();
        $rows = $detalle->cargaCondicion("Id", "IDArticulo='{$idArticulo}'");
        unset($detalle);

        foreach ($rows as $row) {
            $propiedad = new ArticulosPropiedades($row['Id']);
            $array[] = array(
                'titulo' => $propiedad->getIDPropiedad()->getTitulo(),
                'valor' => $propiedad->getIDValor()->getValor(),
            );
        }
        unset($propiedad);

        return $array;
    }

    static function getArticulosPaginadosUsuario($idUsuario = '', $orden = '', $nPagina = '', $nItems = '') {

        if ($idUsuario == '')
            $idUsuario = $_SESSION['usuarioWeb']['id'];

        $var = CpanVariables::getVariables("Web", "Mod", "Articulos");

        if ($orden == '')
            $orden = $var['especificas']['CriterioOrden'];
        if ($orden == '')
            $orden = "PublishedAt DESC";

        if ($nPagina <= 0)
            $nPagina = 1;

        if ($nItems <= 0)
            $nItems = $var['especificas']['NumArticulosListado'];
        if ($nItems <= 0)
            $nItems = 4;

        $filtro = "IDCliente='{$idUsuario}' and ((Vigente='1') or IDCliente='{$_SESSION['usuarioWeb']['id']}')";

        Paginacion::paginar("Articulos", $filtro, $orden, $nPagina, $nItems);

        foreach (Paginacion::getRows() as $row)
            $articulos[] = new Articulos($row['IDArticulo']);

        return array(
            'articulos' => $articulos,
            'paginacion' => Paginacion::getPaginacion(),
        );
    }

    /**
     * Devuelve un array de objetos Articulos que estén en alguno de
     * los estados indicados en el array $estados
     * 
     * @param array $estados Array de estados
     * @param integer $nItems Número de artículos a devolver. Por defecto todos
     * @return array Array de objetos Articulos
     */
    static function getArticulosEstado($estados = array(), $nItems = 0) {

        $array = array();

        if (is_array($estados)) {
            foreach ($estados as $estado) {
                for ($i = 1; $i <= 5; $i++)
                    $filtro .= "(IDEstado{$i}='{$estado}') OR";
            }
            $filtro = substr($filtro, -3);
        }
        else
            $filtro = "";

        $limite = ($nItems > 0) ? "LIMIT {$nItems}" : "";


        $articulo = new Articulos();
        $rows = $articulo->cargaCondicion("IDArticulo", $filtro, "SortOrder ASC {$limite}");
        unset($articulo);

        foreach ($rows as $row)
            $array[] = self::getArticulo($row['IDArticulo']);

        return $array;
    }

    /**
     * Devuelve un array de objetos artículos que
     * pertenecen a la zona $zona y al controlador $controller
     * 
     * Si no se indica controlador, se toma el que está en curso
     * Si no se indica zona, se devuelven todos los artículos agrupados por zonas
     * 
     * El array tiene tantos elementos como zonas de articulos
     * y a su vez cada zona tiene tantos elementos con artículos haya en dicha zona
     * 
     * @param string $controller El nombre del controlador. Opcional, por defecto el que está en curso
     * @param string $zona El codigo de la zona. Opcional, por defecto todas.
     * @return array Array de objetos artículos
     */
    static function getArticulosZona($controller, $zona = '') {

        $array = array();

        $controller = ucwords($controller);

        $filtroZona = ($zona == '') ? "1" : "Zona='{$zona}'";
        $filtro = "(Controller='{$controller}') AND ({$filtroZona})";
        $itemsPagina = 0;

        $zonas = new CpanEsqueletoWeb();
        $reglas = $zonas->cargaCondicion("Id,Zona,NItems,ItemsPagina", $filtro, "SortOrder ASC");
        unset($zonas);

        $ordenArticulos = new OrdenesArticulos();
        foreach ($reglas as $regla) {

            if ($regla['ItemsPagina'] > $itemsPagina)
                $itemsPagina = $regla['ItemsPagina'];

            $articulos = $ordenArticulos->getArticulos($regla['Id'], $regla['NItems']);

            foreach ($articulos as $articulo)
                $array[$regla['Zona']][$articulo->getIDArticulo()] = $articulo;
        }
        unset($ordenArticulos);

        return $array;
    }

    static function getArticulosRelacionados($idArticulo, $nItems) {

        $articulo = self::getArticulo($idArticulo);
        $idFamilia = $articulo->getIDFamilia()->getIDFamilia();
        if (!$idFamilia)
            $idFamilia = $articulo->getIDCategoria()->getIDFamilia();

        return ErpFamilias::getArticulosRelacionados($idFamilia, $nItems);
    }

}

?>
