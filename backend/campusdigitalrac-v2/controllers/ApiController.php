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
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->modelo = new Modelos\ApiModel();
        $this->exception = new ExceptionController();
    }

    public function holaMundo()
    {
        $response = null;

        try {
            $response = $this->modelo->holaMundo();
        } catch (Exception $ex) {
            $this->exception->newException($ex);
        }

        return $response;
    }
}
