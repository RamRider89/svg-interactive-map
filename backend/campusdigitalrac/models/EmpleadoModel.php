<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE MODELO
 * getEmpleado		 	-> 
 * obtenerLineaDenuncia 			-> 
 * 
 * 
 * */

namespace Coppel\Campusdigitalrac\Models;

use PDO;
use Phalcon\Mvc\Model as Modelo;
use Phalcon\DI\DI;

class EmpleadoModel extends Modelo
{
    // POST GET EMLEADO (getEmpleado)
	// POST | api/getEmpleado/
    public function getEmpleado($argumentos)
    {
        $db = DI::getDefault()->get('Personal_des');
        $statement = $db->prepare('EXEC campus_get_empleado ?');
        $statement->bindParam(1, $argumentos->idEmpleado, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getEmpleadoCompleto($idEmpleado)
    {
        $db = DI::getDefault()->get('Personal_des');
        $statement = $db->prepare('EXEC campus_get_empleado_completo :user');
        $statement->bindParam(':user', $idEmpleado, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoTrabajo($idTipo)
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_tipo_trabajo_user :tipo');
        $statement->bindParam(':tipo', $idTipo, PDO::PARAM_INT); 
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getLiderCentro($argumentos)
    {
        $db = DI::getDefault()->get('Personal_des');
        $statement = $db->prepare('EXEC campus_get_lider_centro :query, :user, :centro, :nameuser');
        $statement->bindParam(':query', $argumentos->TIPO_QUERY, PDO::PARAM_STR); 
        $statement->bindParam(':user', $argumentos->idUsuario, PDO::PARAM_STR); 
        $statement->bindParam(':centro', $argumentos->idCentro, PDO::PARAM_STR); 
        $statement->bindParam(':nameuser', $argumentos->nombreEmpleado, PDO::PARAM_STR); 
        $statement->execute();
        return $statement->fetchAll();
    }

    public function obtenerLineaDenuncia()
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC Linea_Denuncia_Obtener');
        $statement->execute();
        return $statement->fetchAll();
    }

}
