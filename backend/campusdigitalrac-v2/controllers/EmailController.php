<?php
/**
 * Api CAMPUS DIGITAL
 * Carlos Duarte
 * david.duarte@coppel.com
 * 
 * */

namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Utilerias as Utilerias;

class EmailController extends RESTController
{
    private $logger;
    private $utilerias;
    private $exception;

    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->utilerias = new Utilerias\Generales();
        $this->email_template = file_get_contents('controllers/PlantillaCorreo.html');
    }


    public function enviarCorreo($parametros) {
        try {
            $parametros->asunto = ($parametros->asunto == '') ? 'Confirmación' : $parametros->asunto;
            $parametros->cuerpo = ($parametros->cuerpo == '') ? 'Confirmación de lugar de trabajo' : $parametros->cuerpo;
            $parametros->correo = ($parametros->correo == '') ? '' : $parametros->correo;
            $parametros->codigo = ($parametros->codigo == '') ? '' : $parametros->codigo;
            
            $message = $this->email_template;
            $message = str_replace('%EncabezadoContenido%', 'Código de confirmación', $message);
            $message = str_replace('%CodigoEnviado%', $parametros->codigo, $message);

            $des_asunto = $parametros->asunto;
            $des_cuerpo = $message; // codigo
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


}
