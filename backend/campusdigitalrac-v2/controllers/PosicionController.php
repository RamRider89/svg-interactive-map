<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE CONTROLADOR
 * getPosicion		 	-> 
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

class PosicionController extends RESTController
{
    private $logger;
    private $model;
    private $utilerias;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\PosicionModel();
        $this->utilerias = new Utilerias\Generales();
    }


    // GET | busqueda de Posicion por identificador
    public function getPosicion($idPosicion){
        try{
            $idPosicion = ($idPosicion == '') ? '0' : $idPosicion;
            $response = $this->model->getPosicion($idPosicion);
            
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


    // FILTRAR Posicion
    // POST | busqueda de Posicion por nombre
    public function getPosicionByName(){
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->nombrePosicion = ($parametros->nombrePosicion == '') ? NULL : $parametros->nombrePosicion;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getPosicionByName($parametros->nombrePosicion);
            
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

    // FILTRAR Posiciones
    // POST | busqueda de Posiciones
    public function getPosiciones(){
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->asignada = ($parametros->asignada == '') ? 0 : $parametros->asignada;
            $parametros->asignada = ($parametros->asignada > 2) ? 2 : $parametros->asignada;
            $mensaje = ['Consulta de posiciones disponibles', 'Consulta de posiciones asignadas', 'Consulta de todas las posiciones'];

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getPosicionesTodas($parametros->asignada);
            
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
        return $this->respond(['response'=> $response, 'mensaje'=> $mensaje[$parametros->asignada]]);
    }

    // POST | GEENRAR CODIGO CONFIRMACION
    public function getCodigoConfirmacion(){
        /**
        *    [idEmpleado] [int],
        *    [emailEmpleado] [varchar](250),
        *    [idCentro] [int],
        *    [idPosition] [int],
        *    [codigo] [int],
        *    [tipoMovimiento] [nchar](10),
        *    [fechaRegistro] [datetime],
        *    [caducado] [bit],
         */

        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->TIPO_QUERY = ($parametros->TIPO_QUERY == '') ? NULL : $parametros->TIPO_QUERY;
            $parametros->idEmpleado = ($parametros->idEmpleado == '') ? 0 : $parametros->idEmpleado;
            $parametros->emailEmpleado = ($parametros->emailEmpleado == '') ? NULL : $parametros->emailEmpleado;
            $parametros->idCentro = ($parametros->idCentro == '') ? 0 : $parametros->idCentro;
            $parametros->idPosition = ($parametros->idPosition == '') ? 0 : $parametros->idPosition;
            $parametros->tipoMovimiento = ($parametros->tipoMovimiento == '') ? NULL : $parametros->tipoMovimiento;
            // BAJA, CAMBIO
            $parametros->idPositionNew = ($parametros->idPositionNew == '') ? 0 : $parametros->idPositionNew;
            $parametros->codigo = ($parametros->codigo == '') ? 0 : $parametros->codigo;

            $codigo = $response = NULL;

            if ($parametros->TIPO_QUERY == 'REGISTRAR') {
                # REGISTRAR NUEVO CODIGO
                $codigoNuevo = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $parametros->codigo = $codigoNuevo;
            }
            
            $response = $this->model->setCodigoConfirmacion($parametros, intval($parametros->codigo));
            
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
        return $this->respond(['response'=> ['consulta'=> $response, 'codigo'=> $parametros->codigo] ]);
    }


}
