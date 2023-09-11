<?php

namespace Coppel\Campusdigitalrac\Utilerias;

use Coppel\RAC\Controllers\RESTController;
use Coppel\RAC\Exceptions\HTTPException;
use Coppel\Campusdigitalrac\Mail;
use Coppel\Campusdigitalrac\Alfresco;
use \DOMDocument as DOMDocument;
use Phalcon\DI\DI;

/***********************************
 *
 *	FUNCIONES GENERALES
 *
 ************************************/
class Generales
{
    const UNDIVAGO = array(
        'ct' => "OQXdDYx5HxTcdTr39LyhBU2rgJDImeJ65v4t+x5Ki+k=",
        "iv" => "42cf781b32b4c11bf39b05d2be4510da",
        "s" => "00c178d7d396b8c1"
    );
    //Funcion que se usa para consultar servicios SOAP
    public function consumirSoap($parametros)
    {
        $response = null;
        $configSoap = DI::getDefault()->get('config')->serviciosSoap;
        $env = DI::getDefault()->get('config')->environment;
        $nomWsdl = $parametros->nombreWsdl;
        $wsdl = $configSoap->$env->$nomWsdl;
        $function = $parametros->servicename;

        $context = stream_context_create(
            array(
                'ssl' => array(
                    'verify_peer' => true,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            )
        );

        $client = new \SoapClient(
            $wsdl,
            array(
                'encoding' => 'UTF-8',
                'soap_version' => $configSoap->version,
                'trace' => $configSoap->trace,
                'exceptions' => $configSoap->exceptions,
                'login' => $configSoap->user,
                'password' => $configSoap->pass,
                'stream_context' => $context
            )
        );

        $response = $client->__soapCall($function, $parametros->paramsSoap);

        //Se valida si la respuesta es un json o un objeto stdclass
        $response = !is_object($response) ? json_decode($response, true) :  $response;
        return $response;
    }

    public function eliminarArchivo($ruta, $archivo, $opcRaiz = 1)
    {
        $undivago = $this->Decrypt('', json_encode(self::UNDIVAGO));
        $alfrescoConfig = (object)$this->Decrypt($undivago, json_encode(DI::getDefault()->get('config')->Alfresco));

        $alfresco = new Alfresco\Alfresco(
            $alfrescoConfig->servidor,
            $alfrescoConfig->puerto,
            $alfrescoConfig->usuario,
            $alfrescoConfig->pass
        );

        switch ($opcRaiz) {
            case 1:
                $rutaRaiz = $alfrescoConfig->pathRaiz;
                break;
            case 2:
                $rutaRaiz = $alfrescoConfig->pathRaiz2;
                break;
            default:
                $rutaRaiz = $alfrescoConfig->pathRaiz;
                break;
        }
        $ruta =  (strlen($ruta) == 1 && $ruta == "/") ? "" : $ruta;
        $ruta = $this->removeAccents($ruta);
        $ruta = $rutaRaiz . $ruta;
        $archivo = $this->clearFileName($archivo);
        $borrado = $alfresco->deleteFile($ruta, $archivo);

        return $borrado;
    }

    public function putArchivo($archivo, $base64, $pathRuta, $opcRaiz = 1)
    {
        $undivago = $this->Decrypt('', json_encode(self::UNDIVAGO));
        $alfrescoConfig = (object)$this->Decrypt($undivago, json_encode(DI::getDefault()->get('config')->Alfresco));

        $alfresco = new Alfresco\Alfresco(
            $alfrescoConfig->servidor,
            $alfrescoConfig->puerto,
            $alfrescoConfig->usuario,
            $alfrescoConfig->pass
        );

        switch ($opcRaiz) {
            case 1:
                $pathRaiz = $alfrescoConfig->pathRaiz;
                break;
            case 2:
                $pathRaiz = $alfrescoConfig->pathRaiz2;
                break;
            default:
                $pathRaiz = $alfrescoConfig->pathRaiz;
                break;
        }

        if (!$alfresco->nodeExists($pathRaiz)) {
            $alfresco->createNode($pathRaiz, $pathRaiz, '', '');
        }
        $strExt = explode(".", $archivo);
        $strExt = end($strExt);

        //CREAR ARCHIVO
        $archivo = $this->clearFileName($archivo);
        $archivo_local = $archivo;
        file_put_contents($archivo, base64_decode($base64));
        $pathRuta =  (strlen($pathRuta) == 1 && $pathRuta == "/") ? "" : $pathRuta;
        $pathRuta = $this->removeAccents($pathRuta);
        $responseNode = $alfresco->nodeExists($pathRaiz . '/' . $pathRuta);
        if (!$responseNode) {
            //Sino existe crearla
            $arrayPath = explode("/", $pathRuta);
            $nodes = $arrayPath;
            $limit = sizeof($nodes);
            for ($i = 0; $i < $limit; $i++) {
                $alfresco->createNode($pathRaiz, $nodes[$i], '', '');
                $pathRaiz .= '/' . $nodes[$i];
            }
        } else {
            $pathRaiz = $pathRaiz . '/' . $pathRuta;
        }

        //Armar el array de array's files
        $files_local = array(
            'files' => array(
                'name' => $archivo_local,
                'tmp_name' => $archivo_local,
                'type' => $strExt,
                'error' => 0,
                'size' => filesize($archivo_local)
            )
        );

        $subidaArchivo = $alfresco->uploadFile($pathRaiz, $files_local);
        //Validacion de subida
        if (!$subidaArchivo['loaded']) {
            $subidaArchivo['mensaje'] = 'Error al subir archivo al repositorio de Alfresco. Error: ';
            $subidaArchivo['mensaje'] .= $subidaArchivo['error'];
        } else {
            unlink($archivo_local);
            $subidaArchivo['mensaje'] = ($pathRaiz . '/' . $archivo_local);
            $subidaArchivo['archivo'] = $archivo_local;
            $subidaArchivo['ruta'] = $pathRaiz;
        }

        return $subidaArchivo;
    }

    public function descargarArchivo($ruta, $archivo, $opcDescarga, $opcRaiz = 1)
    {
        try {
            $undivago = $this->Decrypt('', json_encode(self::UNDIVAGO));
            $alfrescoConfig = (object)$this->Decrypt($undivago, json_encode(DI::getDefault()->get('config')->Alfresco));

            $alfresco = new Alfresco\Alfresco(
                $alfrescoConfig->servidor,
                $alfrescoConfig->puerto,
                $alfrescoConfig->usuario,
                $alfrescoConfig->pass
            );

            switch ($opcRaiz) {
                case 1:
                    $rootPath = $alfrescoConfig->pathRaiz;
                    break;
                case 2:
                    $rootPath = $alfrescoConfig->pathRaiz2;
                    break;
                default:
                    $rootPath = $alfrescoConfig->pathRaiz;
                    break;
            }
            $filePath =  (strlen($ruta) == 1 && $ruta == "/") ? "" : $ruta;
            $fileName = $archivo;
            $arrFile = explode(".", $fileName);
            $extension = end($arrFile);
            $fileName = str_replace('.' . $extension, '', $fileName);
            $fileName = $this->clearFileName($fileName);
            $fileName .= '.' . $extension;
            $filePath = $this->removeAccents($ruta);
            $filePath = str_replace("../", "", $filePath);
            $rootPath .= $filePath;

            if ($alfresco != null) {
                $uploadResponse = $alfresco->findFileJur($rootPath, $fileName);
            }

            $data = $uploadResponse;
            if ($data["total"] == 1) {
                try {
                    if ($opcDescarga ==  1) {
                        if (file_put_contents("$fileName", $data['datos'])) {
                            $docName = $fileName;
                            header("Content-Description: File Transfer");
                            header("Content-Type: application/octet-stream");
                            header("Content-Disposition: attachment; filename=\"$docName\"");
                            readfile($fileName);
                            unlink($fileName);
                            die();
                        } else {
                            $response = array('success' => false, 'msg' => 'El archivo no se ha podido generar en el servidor. Por favor vuelva a intentarlo.');
                        }
                    } else {
                        $response = array('success' => true, 'msg' => $data['datos']);
                    }
                } catch (Exception $e) {
                    $response = array('success' => false, 'msg' => 'Ocurrio un error al descargar el archivo.', 'error' => $e->getMessage());
                }
            } else {
                $response = array('success' => false, 'msg' => 'El archivo no se encuentra en el servidor.');
            }
        } catch (Exception $e) {
            $response = array('success' => false, 'msg' => 'Ocurrio un error al descargar el archivo.', 'error' => $e->getMessage());
        }

        return $response;
    }



    public function buscarArchivo($ruta, $archivo, $opcRaiz = 1)
    {
        try {
            $undivago = $this->Decrypt('', json_encode(self::UNDIVAGO));
            $alfrescoConfig = (object)$this->Decrypt($undivago, json_encode(DI::getDefault()->get('config')->Alfresco));

            $alfresco = new Alfresco\Alfresco(
                $alfrescoConfig->servidor,
                $alfrescoConfig->puerto,
                $alfrescoConfig->usuario,
                $alfrescoConfig->pass
            );

            switch ($opcRaiz) {
                case 1:
                    $rootPath = $alfrescoConfig->pathRaiz;
                    break;
                case 2:
                    $rootPath = $alfrescoConfig->pathRaiz2;
                    break;
                default:
                    $rootPath = $alfrescoConfig->pathRaiz;
                    break;
            }
            $filePath =  (strlen($ruta) == 1 && $ruta == "/") ? "" : $ruta;
            $fileName = $archivo;
            $arrFile = explode(".", $fileName);
            $extension = end($arrFile);
            $fileName = str_replace('.' . $extension, '', $fileName);
            $fileName = $this->clearFileName($fileName);
            $fileName .= '.' . $extension;
            $filePath = $this->removeAccents($ruta);
            $filePath = str_replace("../", "", $filePath);
            $rootPath .= $filePath;

            if ($alfresco != null) {
                $uploadResponse = $alfresco->findFileJur($rootPath, $fileName);
            }
            $data = $uploadResponse;
            if (isset($data)) {
                if ($data["total"] == 1) {
                    $response = array(
                        'success' => true,
                        'msg' => 'Archivo encontrado.'
                    );
                } else {
                    $response = array(
                        'success' => false,
                        'msg' => 'El archivo no se encuentra en el servidor.'
                    );
                }
            } else {
                $response = array(
                    'success' => false,
                    'msg' => 'El archivo no se encuentra en el servidor.'
                );
            }
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'msg' => 'Ocurrio un error al descargar el archivo.',
                'error' => $e->getMessage()
            );
        }

        return $response;
    }

    //creamos la función para mensajes
    public function mensajesGuardado($resultado)
    {
        //creamos la variable mensaje
        $mensaje = null;

        switch ($resultado) {
            case 3:
                //Actualizado correctamente
                $mensaje = array("mensaje" => "Actualizado Correctamente", "registrado" => true);
                break;
            case 2:
                //Eliminado correctamente
                $mensaje = array("mensaje" => "Eliminado Correctamente", "registrado" => true);
                break;
            case 1:
                // Guardado correctamente
                $mensaje = array("mensaje" => "Guardado Correctamente", "registrado" => true);
                break;

            case 0:
                // Error al guardar
                $mensaje = array("mensaje" => "Error al guardar", "registrado" => false);
                break;

            default:
                // code...
                break;
        }

        return $mensaje;
    }

    function obtenerDatosXml($xmlString)
    {
        $dom = new DomDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadxml($xmlString);
        $datos = [];

        $comprobante = $dom->getElementsByTagName('Comprobante');
        $timbreFiscal = $dom->getElementsByTagName('TimbreFiscalDigital');

        // Version 3.2
        $folio = 'folio';

        foreach ($comprobante as $params) {
            $datos['version'] =  ($params->getAttribute('version'));

            if ($datos['version']  == '') {
                // Version 3.3
                $folio = 'Folio';
            }

            $datos['folio'] = ($params->getAttribute($folio));
        }

        foreach ($timbreFiscal as $params) {
            $datos['UUID'] = ($params->getAttribute('UUID'));
        }

        return $datos;
    }

    function clearFileName($string)
    {
        $string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
        $string = str_replace(
            array(
                "\\", "¨", "º", "~", "#", "@", "|", "!", "\"",
                "°", "·", "$", "%", "&", "/", "?", "'", "¡",
                "¿", "[", "^", "`", "]", "+", "}", "{", "¨",
                "´", ">", "<", ";", ":", ","
            ),
            '',
            $string
        );
        return $string;
    }

    function removeAccents($string)
    {
        $string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        return $string;
    }

    public function envioCorreo($des_asunto, $des_cuerpo, $des_correo)
    {
        $Lista_Correos = '';
        //creamos un objeto de la clase PHPMailer para enviar correo
        $mail = new Mail\PHPMailer();
        $mailConfig = DI::getDefault()->get('config')->MAIL;

        //Añadimos todos los datos requeridos para el Mailer
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $mailConfig->ip;
        $mail->Username = $mailConfig->usuario;
        $mail->Password = $mailConfig->contrasena;
        $mail->Port = $mailConfig->puerto;
        $mail->From = $mailConfig->correo;
        // se agrega para uso de autenticacion mediante tls
        $mail->SMTPSecure = 'tls';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => true,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );
        $mail->isHTML(true);
        $mail->FromName = 'Campus Digital';

        //Optiene la lista de los correos a los que se enviara correo
        $Lista_Correos = explode(',', $des_correo);
        for ($h = 0; $h < count($Lista_Correos); $h++) {
            $mail->addAddress($Lista_Correos[$h]);
        }

        $mail->Subject = ($des_asunto);

        //Creamos el cuerpo del correo
        $txtMail = ($des_cuerpo);

        //Añadimos el texto al atributo body del objeto mail
        $mail->Body = $txtMail;

        //Se establece el conjunto de caracteres a UTF-8 para que acepte caracteres especiales en el subject
        $mail->CharSet = 'UTF-8';

        //Llamamos al metodo send del objeto mail para enviar correo
        return $mail->send();
    }

    function Decrypt($passphrase, $jsonString)
    {
        $jsondata = json_decode($jsonString, true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv  = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    function holaMundo()
    {
        return "Hola Generales";
    }
}
