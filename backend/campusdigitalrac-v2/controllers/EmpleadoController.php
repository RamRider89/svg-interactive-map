<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * EN ESTE CONTROLADOR
 * getEmpleado		 	-> 
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

class EmpleadoController extends RESTController
{
    private $logger;
    private $model;
    private $utilerias;
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\EmpleadoModel();
        $this->utilerias = new Utilerias\Generales();
        $this->exception = new ExceptionController();
    }


    // FILTRAR EMPLEADO
    // POST | busqueda de empleado por id coppel
    public function getEmpleado(){
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->idEmpleado = ($parametros->idEmpleado == '') ? 0 : $parametros->idEmpleado;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getEmpleado($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    // POST | busqueda de empleado por id coppel
    public function getEmpleadoCompleto(){
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->idEmpleado = ($parametros->idEmpleado == '') ? 0 : $parametros->idEmpleado;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getEmpleadoCompleto($parametros->idEmpleado);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    // GET | busqueda de TiposTrabajo
    public function getTipoTrabajo($idTipo){
        try{
            $idTipo = ($idTipo == '') ? '0' : $idTipo;
            $response = $this->model->getTipoTrabajo($idTipo);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    // POST | busqueda de Lideres de Centro
    public function getLiderCentro(){
        // TIPO_QUERY = CENTRO, ID, NOMBRE
        try{
            $parametros = $this->request->getJsonRawBody();
            $parametros->TIPO_QUERY = ($parametros->TIPO_QUERY == '') ? 'CENTRO' : $parametros->TIPO_QUERY;
            $parametros->idUsuario = ($parametros->idUsuario == '') ? null : $parametros->idUsuario;
            $parametros->idCentro = ($parametros->idCentro == '') ? null : $parametros->idCentro;
            $parametros->nombreEmpleado = ($parametros->nombreEmpleado == '') ? null : $parametros->nombreEmpleado;

            if (!isset($parametros)) {
                throw new HTTPException("Estructura de datos no válida.", 404);
            }
            $response = $this->model->getLiderCentro($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    public function obtenerLineaDenuncia()
    {
        try
        {
            $response = $this->model->obtenerLineaDenuncia();

        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    public function enviarCorreo() {
        try {

            $des_asunto = $this->utilerias->holaMundo();
            $des_cuerpo = "Hola Mundo este es un ejemplo de email desde RAC."; 
            $des_correo = "david.duarte@coppel.com"; 
            $response = new \stdClass();

            if ($des_correo != '') {
                $response->enviado = $this->utilerias->envioCorreo($des_asunto, $des_cuerpo, $des_correo);
                $response->mensaje = 'Se ha enviado el correo.';
            } else {
                $response->enviado = false;
                $response->mensaje = 'No se envio el correo, el e-mail no esta definido.';
            }
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $response;
    }


    public function actualizarProveedor() {
        $parametros = $this->request->getJsonRawBody();
        if (!isset($parametros)) {
            throw new HTTPException("Estructura de datos no válida.", 404);
        }
        try {
            $response = $this->model->actualizarProveedor($parametros);
            $respuesta = new \stdClass();
            if ($response[0]['resultado'] > 0) {
                //Se envia correo
                $datosCorreo = $this->model->correoRegistro($parametros);
                $respuesta->enviado = $this->utilerias->envioCorreo(
                    $datosCorreo[0]['des_asunto'],
                    $datosCorreo[0]['des_cuerpo'],
                    $parametros->de_eMail
                );
            } else {
                $respuesta->enviado = false;
            }
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $response;
    }

}
