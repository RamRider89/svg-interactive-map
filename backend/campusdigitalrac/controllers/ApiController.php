<?php

namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Models as Modelos;

class ApiController extends RESTController
{
    private $logger;
    private $modelo;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->modelo = new Modelos\ApiModel();
    }

    public function holaMundo()
    {
        $response = null;

        try {
            $response = $this->modelo->holaMundo();
        } catch (Exception $ex) {
            $mensaje = $ex->getMessage();
            $this->logger->error('['. __METHOD__ ."] Excepción > $mensaje");

            throw new HTTPException(
                'No fue posible completar su solicitud, intente de nuevo o contacte con el administrador.',
                500, [
                    'dev' => $mensaje,
                    'internalCode' => 'SIE1000',
                    'more' => 'Verificar conexión con la base de datos.'
                ]
            );
        }

        return $response;
    }
}
