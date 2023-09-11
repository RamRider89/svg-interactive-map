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

class UserPosicionController extends RESTController
{
    private $logger;
    private $model;
    private $utilerias;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\UserPosicionModel();
        $this->utilerias = new Utilerias\Generales();
    }

    public function getCodigoConfirmacion(){

        try {

            $parametros = $this->request->getJsonRawBody();
            $parametros->asunto = ($parametros->asunto == '') ? 'Confirmación' : $parametros->asunto;
            $parametros->cuerpo = ($parametros->cuerpo == '') ? 'Confirmación de lugar de trabajo' : $parametros->cuerpo;
            $parametros->correo = ($parametros->correo == '') ? '' : $parametros->correo;

            $des_asunto = $parametros->asunto;
            $des_cuerpo = $parametros->cuerpo; // codigo de confirmacion
            $des_correo = $parametros->correo; 
            $response = new \stdClass();

            if ($des_correo != '') {
                $response->enviado = $this->utilerias->envioCorreo($des_asunto, $des_cuerpo, $des_correo);
                $response->mensaje = 'Se ha enviado el correo.';
            } else {
                $response->enviado = false;
                $response->mensaje = 'No se envio el correo, el e-mail no esta definido.';
            }
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
        return $response;

    }

    public function enviarCorreoConfirmacion() {
        try {

            $parametros = $this->request->getJsonRawBody();
            $parametros->asunto = ($parametros->asunto == '') ? 'Confirmación' : $parametros->asunto;
            $parametros->cuerpo = ($parametros->cuerpo == '') ? 'Confirmación de lugar de trabajo' : $parametros->cuerpo;
            $parametros->correo = ($parametros->correo == '') ? '' : $parametros->correo;

            $des_asunto = $parametros->asunto;
            $des_cuerpo = $parametros->cuerpo; // codigo de confirmacion
            $des_correo = $parametros->correo; 
            $response = new \stdClass();

            if ($des_correo != '') {
                $response->enviado = $this->utilerias->envioCorreo($des_asunto, $des_cuerpo, $des_correo);
                $response->mensaje = 'Se ha enviado el correo.';
            } else {
                $response->enviado = false;
                $response->mensaje = 'No se envio el correo, el e-mail no esta definido.';
            }
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
        return $response;
    }

    // FILTRAR Posicion
    // POST | enviar email
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



}
