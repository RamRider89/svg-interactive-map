<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE CONTROLADOR
 * getContactos		 	-> 
 * */

namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Models as Modelos;
use Coppel\Campusdigitalrac\Utilerias as Utilerias;

class ContactosController extends RESTController
{
    private $logger;
    private $model;
    private $utilerias;
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\ContactosModel();
        $this->utilerias = new Utilerias\Generales();
        $this->exception = new ExceptionController();
    }

    // POST | busqueda de Contactos
    public function getContactos(){
        try{
            // ARGS: @TIPO_QUERY VARCHAR, @idBusqueda, @strBusqueda VARCHAR
            $parametros = $this->request->getJsonRawBody();
            $parametros->TIPO_QUERY = ($parametros->TIPO_QUERY == '') ? 'ALL' : $parametros->TIPO_QUERY;
            $parametros->idBusqueda = ($parametros->idBusqueda == '') ? 0 : $parametros->idBusqueda;
            $parametros->nombreEdificio = ($parametros->strBusqueda == '') ? NULL : $parametros->strBusqueda;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getContactos($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

}
