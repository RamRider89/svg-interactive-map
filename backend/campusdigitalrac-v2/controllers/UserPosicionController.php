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
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->model = new Modelos\UserPosicionModel();
        $this->utilerias = new Utilerias\Generales();
        $this->exception = new ExceptionController();
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
            $this->exception->newException($ex);
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
            $this->exception->newException($ex);
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
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> $response]);
    }

    // POST | GUARDAR USER INFO POSICION
    // POST | api/setuserposition/
    public function setUserPosicion(){
        try{
            $parametros = $this->request->getJsonRawBody();
            // setUserPosition
            $parametros->idEmpleado = ($parametros->idEmpleado == '') ? 0 : $parametros->idEmpleado;
            $parametros->nombre = ($parametros->nombre == '') ? '' : $parametros->nombre;
            $parametros->apellidoPaterno = ($parametros->apellidoPaterno == '') ? '' : $parametros->apellidoPaterno;
            $parametros->apellidoMaterno = ($parametros->apellidoMaterno == '') ? '' : $parametros->apellidoMaterno;
            $parametros->numeroPuesto = ($parametros->numeroPuesto == '') ? 0 : $parametros->numeroPuesto;
            $parametros->numeroCentro = ($parametros->numeroCentro == '') ? 0 : $parametros->numeroCentro;
            $parametros->empleadoEmaiL = ($parametros->empleadoEmaiL == '') ? '' : $parametros->empleadoEmaiL;
            $parametros->numeroTelefono = ($parametros->numeroTelefono == '') ? 0 : $parametros->numeroTelefono;
            $parametros->numeroLider = ($parametros->numeroLider == '') ? 0 : $parametros->numeroLider;
            $parametros->numeroGerente = ($parametros->numeroGerente == '') ? 0 : $parametros->numeroGerente;
            $parametros->numeroEmpresa = ($parametros->numeroEmpresa == '') ? 0 : $parametros->numeroEmpresa;
            $parametros->tipoTrabajo = ($parametros->tipoTrabajo == '') ? 0 : $parametros->tipoTrabajo;
            $parametros->fechaNacimiento = ($parametros->fechaNacimiento == '') ? NULL : $parametros->fechaNacimiento;
            $parametros->fotoURL = ($parametros->fotoURL == '') ? NULL : $parametros->fotoURL;

            // GUANDANDO USER POSICION
            $response = $this->model->setUserPosition($parametros);
            
        } catch (\Exception $ex) {
            $this->exception->newException($ex);
        }
        return $this->respond(['response'=> ['setUserPosition' => $response ]]);
    }



}
