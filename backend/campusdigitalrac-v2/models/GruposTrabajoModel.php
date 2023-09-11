<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE MODELO
 * getTiposGruposTrabajo		 	-> 
 * */

namespace Coppel\Campusdigitalrac\Models;

use PDO;
use Phalcon\Mvc\Model as Modelo;
use Phalcon\DI\DI;

class GruposTrabajoModel extends Modelo
{
    public function getTiposGruposTrabajo($idTipo)
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $response = null;
        $statement = $db->prepare('EXEC campus_get_tipos_grupo_trabajo :tipo');
        $statement->bindParam(':tipo', $idTipo, PDO::PARAM_INT); 
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getGruposTrabajo($argumentos)
    {
        // ARGS: @TIPO_QUERY VARCHAR,  @idBusqueda INT, @nombreEdificio VARCHAR, @lugarAsignado BIT
        $db = DI::getDefault()->get('ConstruNet_des');
        $response = null;
        $statement = $db->prepare('EXEC campus_get_grupo_trabajo :TIPO_QUERY, :idBusqueda, :nombreEdificio, :lugarAsignado');
        $statement->bindParam(':TIPO_QUERY', $argumentos->TIPO_QUERY, PDO::PARAM_STR); 
        $statement->bindParam(':idBusqueda', $argumentos->idBusqueda, PDO::PARAM_INT); 
        $statement->bindParam(':nombreEdificio', $argumentos->nombreEdificio, PDO::PARAM_STR); 
        $statement->bindParam(':lugarAsignado', $argumentos->lugarAsignado, PDO::PARAM_INT); 
        $statement->execute();
        return $statement->fetchAll();
    }

    // POST GET GruposTrabajoES (getGruposTrabajoesTodas)
	// POST | api/getGruposTrabajo/
    public function getGruposTrabajoesTodas($asignada)
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_GruposTrabajoes :asignada');
        $statement->bindParam(':asignada', $asignada, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

}
