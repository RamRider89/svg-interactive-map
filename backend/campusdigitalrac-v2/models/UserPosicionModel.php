<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE MODELO
 * getPosicion		 	-> 
 * UsersPositionsModel
 * */

namespace Coppel\Campusdigitalrac\Models;

use PDO;
use Phalcon\Mvc\Model as Modelo;
use Phalcon\DI\DI;

class UserPosicionModel extends Modelo
{
    // POST GET POSICION (getPosicion)
	// POST | api/getPosicion/
    public function getPosicion($posicion)
    {      
        $posicion = intval($posicion);
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_posicion ?');
        $statement->bindParam(1, $posicion, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    // POST | api/getPosicionByName/
    public function getPosicionByName($nombrePosicion)
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_posicion_byname ?');
        $statement->bindParam(1, $nombrePosicion, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll();
        
    }

    // POST GET POSICIONES (getPosicionesTodas)
	// POST | api/getPosicion/
    public function getPosicionesTodas($asignada)
    {
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_posiciones :asignada');
        $statement->bindParam(':asignada', $asignada, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    // ERROR
    public function getPosicionProcedure($posicion)
    {      
        $posicion = intval($posicion);
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_get_posicion ?');
        $statement->bindParam(1, $posicion, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

}
