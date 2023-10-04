<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE MODELO
 * getPosicion		 	-> 
 * */

namespace Coppel\Campusdigitalrac\Models;

use PDO;
use Phalcon\Mvc\Model as Modelo;
use Phalcon\DI\DI;

class PosicionModel extends Modelo
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

    // POST GENERAR CODIGO PARA CONFIRMAR POSICION (setCodigoConfirmacion)
	// POST | api/codigoconfirmacion/
    public function setCodigoConfirmacion($parametros, $codigo){
        /**
        *    [idEmpleado] [int],
        *    [emailEmpleado] [varchar](250),
        *    [idCentro] [int],
        *    [idPosition] [int],
        *    [tipoMovimiento] [nchar](10),
         */

        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_codigo_confirmacion :query, :empleado, :correo, :centro, :posicion, :codigo, :tipomov');
        $statement->bindParam(':query', $parametros->TIPO_QUERY, PDO::PARAM_STR);
        $statement->bindParam(':empleado', $parametros->idEmpleado, PDO::PARAM_INT);
        $statement->bindParam(':correo', $parametros->emailEmpleado, PDO::PARAM_STR);
        $statement->bindParam(':centro', $parametros->idCentro, PDO::PARAM_INT);
        $statement->bindParam(':posicion', $parametros->idPosition, PDO::PARAM_INT);
        $statement->bindParam(':codigo', $codigo, PDO::PARAM_INT);
        $statement->bindParam(':tipomov', $parametros->tipoMovimiento, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll();

    }

    // POST ASGINAR POSICION (setPosition)
	// POST | api/setposition/
    public function setPosition($parametros){
         /**
        *    [idPosition] [int],
        *    [idEmpleado] [int],
         */
        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_asignar_posicion :mov, :posicion, :empleado');
        $statement->bindParam(':mov', $parametros->TIPO_MOV, PDO::PARAM_INT);
        $statement->bindParam(':posicion', $parametros->idPosition, PDO::PARAM_INT);
        $statement->bindParam(':empleado', $parametros->idEmpleado, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();

    }

}
