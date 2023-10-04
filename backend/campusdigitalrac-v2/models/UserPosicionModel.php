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

        // POST ASGINAR SUER POSICION (setUserPosition)
	// POST | api/setposition/
    public function setUserPosition($parametros){
        /**
          *  @idEmpleado INT, 
          *  @nombre VARCHAR(250), 
          *  @apellidoPaterno VARCHAR(250), 
          *  @apellidoMaterno VARCHAR(250), 
          *  @puesto INT, 
          *  @centro INT, 
          *  @correo VARCHAR(250), 
          *  @telefono INT, 
          *  @lider INT, 
          *  @gerente INT, 
          *  @empresa INT, 
          *  @tipoTrabajo INT, 
          *  @cumpleanos DATE, 
          *  @fotoUrl VARCHAR(250)
          */

        $db = DI::getDefault()->get('ConstruNet_des');
        $statement = $db->prepare('EXEC campus_asignar_user_posicion :empleado, :nombre, :apellidop, :apellidom, :puesto, :centro, :correo, :telefono, :lider, :gerente, :empresa, :tipotr, :cumpleanos, :foto');
        $statement->bindParam(':empleado', $parametros->idEmpleado, PDO::PARAM_INT);
        $statement->bindParam(':nombre', $parametros->nombre, PDO::PARAM_STR);
        $statement->bindParam(':apellidop', $parametros->apellidoPaterno, PDO::PARAM_STR);
        $statement->bindParam(':apellidom', $parametros->apellidoMaterno, PDO::PARAM_STR);
        $statement->bindParam(':puesto', $parametros->numeroPuesto, PDO::PARAM_INT);
        $statement->bindParam(':centro', $parametros->numeroCentro, PDO::PARAM_INT);
        $statement->bindParam(':correo', $parametros->empleadoEmaiL, PDO::PARAM_STR);
        $statement->bindParam(':telefono', $parametros->numeroTelefono, PDO::PARAM_INT);
        $statement->bindParam(':lider', $parametros->numeroLider, PDO::PARAM_INT);
        $statement->bindParam(':gerente', $parametros->numeroGerente, PDO::PARAM_INT);
        $statement->bindParam(':empresa', $parametros->numeroEmpresa, PDO::PARAM_INT);
        $statement->bindParam(':tipotr', $parametros->tipoTrabajo, PDO::PARAM_INT);
        $statement->bindParam(':cumpleanos', $parametros->fechaNacimiento, PDO::PARAM_STR);
        $statement->bindParam(':foto', $parametros->fotoURL, PDO::PARAM_STR);
        
        $statement->execute();
        return $statement->fetchAll();
        
    }
}
