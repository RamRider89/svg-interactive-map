<?php

// namespace Diseno\Campusdigitalrac\Controllers;

// use Coppel\RAC\Controllers\RESTController;
// use Coppel\RAC\Exceptions\HTTPException;
// use Diseno\Campusdigitalrac\Models as Modelos;
// use Diseno\Campusdigitalrac\Libs;
// use Diseno\Campusdigitalrac\Utilerias as Utilerias;
// use Phalcon\DI\DI;
namespace Coppel\Campusdigitalrac\Controllers;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Models as Modelos;

class logsController extends RESTController
{
    private $logger;
    private $mensajeError = "Estructura de datos no válida.";


    public function onConstruct()
    {
        $this->logger = DI::getDefault()->get('logger');
        $this->log = DI::getDefault()->get('config')->log; 
    }

    public function logs()
    {

        try {
             
                $url = $this->log->Dev->authenticate;
                $appId = $this->log->Dev->appId;
                $appKey = $this->log->Dev->appKey;
                $apiLog = $this->log->apiLog;

                $datos =  array('appId'=> $appId, 'appKey'=> $appKey);

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
                $response = curl_exec($curl);
                curl_close($curl);             

                $respuesta = json_decode($response);
                $token = $respuesta->data->token;
         
                // RESIVIENDO DATOS ENVIADOS EN EL POST
                $argumentos = $this->request->getJsonRawBody();

                // $numSistema = "2";
                // $ipCliente = "52.35.60.68";
                // $numUsuario = "976886123";
                // $desEvento = "logidn";
                // $desNavegador = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36";
                // $nomComponente = "/construnet/estimaciones/proveedores_agregar.cfm";
                // $desParametros = "duarte";
                // $desRequest = "POST";
                // $desOrigen = "COLDFUSION";
                // $numConcurrencia = "1";


                $data =  array(
                    'iduSistema'=> $argumentos->numSistema,     // IDENTIFICADOR DEL SISTEMA CLIENTE
                    'numIp'=>  $this->getIPCliente(),           // IP DEL USUARIO CLIENTE
                    'numUsuario'=> $argumentos->numUsuario,     // NUMERO DE USUARIO CLIENTE
                    'desEvento'=> $argumentos->desEvento,       // DECRIPCION DEL EVENTO
                    'desNavegador'=> $argumentos->desNavegador,     // NOMBRE DEL NAVEGADOR WEB CLIENTE
                    'nomComponente'=> $argumentos->nomComponente,   // NOMBRE DEL COMPONENTE EJECUTANDOCE
                    'desParametros'=> $argumentos->desParametros,   // CADENA DE DATOS ENVIADOS EN EL COMPONENTE
                    'desRequest'=> $argumentos->desRequest,         // POST | GET | PUT | DELETE
                    'desOrigen'=> $argumentos->desOrigen,           // DESCRIPCION DEL SISTEMA ORIGEN
                    'numConcurrencia'=> $argumentos->numConcurrencia    // NUMERO DE CONCURRENCIA :1 -> APILOGS ACTUALIZA LA CONCURRENCIA SEGUN APLIQUE
                );
            

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiLog);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: ' . $token
                ));
                $response = curl_exec($ch);
                curl_close($ch);


        } catch (\Exception $ex) {
            $this->lanzarError($ex);
        }
        return $this->respond(['response' => $response]);
    }


    public function lanzarError($error)
    {
        $mensaje = $error->getMessage();
        $this->logger->error('[' . __METHOD__ . "] Se lanzó la excepción > $mensaje");

        throw new HTTPException(
            'No fue posible completar su solicitud, intente de nuevo por favor.',
            500,
            [
                'dev' => $mensaje,
                'internalCode' => 'SIE1000',
                'more' => 'Verificar conexión con la base de datos.'
            ]
        );
    }

    // Funcion para obtener la ip del cliente
    public function getIPCliente() {
        $ipaddress = '0';
            // solo funciona con php 8
            if(!empty($_SERVER['REMOTE_ADDR'])){
                $ipaddress = $_SERVER['REMOTE_ADDR'];
                if ($ipaddress == "::1") {
                    $ipaddress = getHostByName(getHostName()); //localhost
                }
            }
            else{
                $ipaddress = 'UNKNOWN';
            }
        return $ipaddress;
    }
}
