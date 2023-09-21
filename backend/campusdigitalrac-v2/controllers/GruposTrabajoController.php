<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE CONTROLADOR
 * getGruposTrabajo		 	-> 
 * obtenerLineaDenuncia 			-> 
 * 
 * 
 * */

namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Models as Modelos;
use Coppel\Campusdigitalrac\Utilerias as Utilerias;

class GruposTrabajoController extends RESTController
{
    private $logger;
    private $model;
    private $utilerias;
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\GruposTrabajoModel();
        $this->utilerias = new Utilerias\Generales();
        $this->exception = new ExceptionController();
    }


    // GET | busqueda de TiposGruposTrabajo
    public function getTipoGruposTrabajo($idTipo){
        try{
            $idTipo = ($idTipo == '') ? '0' : $idTipo;
            $response = $this->model->getTiposGruposTrabajo($idTipo);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }


    // POST | busqueda de GruposTrabajo
    public function getGruposTrabajo(){
        try{
            // ARGS: @TIPO_QUERY VARCHAR,  @idBusqueda INT, @nombreEdificio VARCHAR, @lugarAsignado BIT
            // TIPO_QUERY = ALL, ID, TIPO, EDIFICIO {EA || EB}, ASIGNADO {0 || 1}, GERENTE {claveCoppel}    

            $parametros = $this->request->getJsonRawBody();
            $parametros->TIPO_QUERY = ($parametros->TIPO_QUERY == '') ? 'ALL' : $parametros->TIPO_QUERY;
            $parametros->idBusqueda = ($parametros->idBusqueda == '') ? 0 : $parametros->idBusqueda;
            $parametros->nombreEdificio = ($parametros->nombreEdificio == '') ? NULL : $parametros->nombreEdificio;
            $parametros->lugarAsignado = ($parametros->lugarAsignado == '') ? 0 : $parametros->lugarAsignado;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getGruposTrabajo($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

}
