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
    private $exception;
    private $email;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\PosicionModel();
        $this->userModel = new Modelos\UserPosicionModel();
        $this->utilerias = new Utilerias\Generales();
        $this->exception = new ExceptionController();
        $this->email = new EmailController();
    }


    // GET | busqueda de Posicion por identificador
    public function getPosicion($idPosicion){
        try{
            $idPosicion = ($idPosicion == '') ? '0' : $idPosicion;
            $response = $this->model->getPosicion($idPosicion);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
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
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    // FILTRAR Posiciones
    // POST | busqueda de Posiciones
    public function getPosiciones(){
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->asignada = ($parametros->asignada == '') ? 0 : $parametros->asignada;
            $parametros->asignada = ($parametros->asignada > 3) ? 3 : $parametros->asignada;
            $mensaje = ['Consulta de posiciones disponibles', 'Consulta de posiciones asignadas', 'Consulta de posiciones asignadas en relacion al usuario', 'Consulta de todas las posiciones'];

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getPosicionesTodas($parametros->asignada);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response, 'mensaje'=> $mensaje[$parametros->asignada]]);
    }

    // POST | GENERAR CODIGO CONFIRMACION
    public function setCodigoConfirmacion(){
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

            // SEND EMAIL
            $correo = '';
            if ($parametros->TIPO_QUERY == 'REGISTRAR') {
                if (boolval($response[0]['CONSULTA']) && boolval($response[0]['REGISTRADO'])) {

                    $args = (object) [        
                    'asunto' => 'Código de Confirmación - Campus Digital',
                    'encabezado' => 'Código de Confirmación',
                    'correo' => $parametros->emailEmpleado,
                    'mensaje' => $parametros->codigo,
                    'mensajeAdicional' => 'Introduzca el código en la pantalla de CampusDigital para verificar su cuenta'
                    ];

                    $correo = $this->email->enviarCorreo($args);
                }
            }
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> ['consulta'=> $response, 'codigo'=> $parametros->codigo, 'correo' => $correo ]]);
    }


    // POST | CONFIRMACION EMAIL
    public function setEmailConfirmacion(){
        

        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->asunto = ($parametros->asunto == '') ? NULL : $parametros->asunto;
            $parametros->emailEmpleado = ($parametros->emailEmpleado == '') ? NULL : $parametros->emailEmpleado;
            $parametros->mensaje = ($parametros->mensaje == '') ? NULL : $parametros->mensaje;

            // SEND EMAIL
            $correo = null;
            $args = (object) [        
            'asunto' => $parametros->asunto,
            'encabezado' => $parametros->asunto,
            'correo' => $parametros->emailEmpleado,
            'mensaje' => $parametros->mensaje,
            'mensajeAdicional' => $parametros->mensajeAdicional
            ];

            $correo = $this->email->enviarCorreo($args);

        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> ['correo' => $correo ]]);
    }


    // POST | GUARDAR POSICION
    // POST | api/setposition/
    public function setPosicion(){
        try{
            $parametros = $this->request->getJsonRawBody();
            // setPosition
            $parametros->TIPO_MOV = ($parametros->TIPO_MOV == '') ? 0 : $parametros->TIPO_MOV;
            $parametros->idPosition = ($parametros->idPosition == '') ? 0 : $parametros->idPosition;
            $parametros->idEmpleado = ($parametros->idEmpleado == '') ? 0 : $parametros->idEmpleado;

            // GUANDANDO POSICION
            $response = $this->model->setPosition($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> ['setPosicion'=> $response ]]);
    }


}
