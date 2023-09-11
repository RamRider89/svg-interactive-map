<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE MODELO
 * getContactos		 	-> 
 * */

namespace Coppel\Campusdigitalrac\Models;

use PDO;
use Phalcon\Mvc\Model as Modelo;
use Phalcon\DI\DI;

class ContactosModel extends Modelo
{
    // POST | api/getContactos/
    public function getContactos($argumentos)
    {
        // ARGS: @TIPO_QUERY VARCHAR, @idBusqueda, @strBusqueda VARCHAR
        $db = DI::getDefault()->get('ConstruNet_des');
        $response = null;
        $statement = $db->prepare('EXEC campus_get_contactos :TIPO_QUERY, :idBusqueda, :strBusqueda');
        $statement->bindParam(':TIPO_QUERY', $argumentos->TIPO_QUERY, PDO::PARAM_STR); 
        $statement->bindParam(':idBusqueda', $argumentos->idBusqueda, PDO::PARAM_INT); 
        $statement->bindParam(':strBusqueda', $argumentos->strBusqueda, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll();
    }

}
