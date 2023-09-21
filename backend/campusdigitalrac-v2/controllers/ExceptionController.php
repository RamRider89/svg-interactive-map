<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * */

namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;

class ExceptionController extends RESTController
{
    private $logger;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
    }

    // return exception
    public function newException($ex) {

        $mensaje = $ex->getMessage();
        $this->logger->error('['. __METHOD__ ."] Se lanzó la excepción > $mensaje");

        return throw new HTTPException(
            'No fue posible completar su solicitud, intente de nuevo por favor.',
            500, [
                'dev' => $mensaje,
                'internalCode' => 'SIE1000',
                'more' => 'Verificar conexión con la base de datos.'
            ]
        );
    }
}
