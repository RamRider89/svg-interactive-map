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

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\GruposTrabajoModel();
        $this->utilerias = new Utilerias\Generales();
    }


    // GET | busqueda de TiposGruposTrabajo
    public function getTipoGruposTrabajo($idTipo){
        try{
            $idTipo = ($idTipo == '') ? '0' : $idTipo;
            $response = $this->model->getTiposGruposTrabajo($idTipo);
            
        } catch (\Exception $ex) {
            $mensaje = $ex->getMessage();
            $this->logger->error('['. __METHOD__ ."] Se lanzó la excepción > $mensaje");

            throw new HTTPException(
                'No fue posible completar su solicitud, intente de nuevo por favor.',
                500, [
                    'dev' => $mensaje,
                    'internalCode' => 'SIE1000',
                    'more' => 'Verificar conexión con la base de datos.'
                ]
            );
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
            $mensaje = $ex->getMessage();
            $this->logger->error('['. __METHOD__ ."] Se lanzó la excepción > $mensaje");

            throw new HTTPException(
                'No fue posible completar su solicitud, intente de nuevo por favor.',
                500, [
                    'dev' => $mensaje,
                    'internalCode' => 'SIE1000',
                    'more' => 'Verificar conexión con la base de datos.'
                ]
            );
        }
        return $this->respond(['response'=> $response]);
    }

}
