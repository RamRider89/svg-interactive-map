<?php
namespace Coppel\Campusdigitalrac\Alfresco;

use Coppel\RAC\Exceptions\HTTPException;

header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

require_once 'cmis_repository_wrapper.php';
require_once 'Logger/Logger.php';

class Alfresco {
    // CONSTANTES
    const NORMAL = 1;
    const JSON = 2;

    // PROPIEDADES
    private $_clientCMIS = null;
    private $_error;
    private $_returnDataType;
    private $_user;
    private $_password;
    private $_url = '';
    private $_ip = '';
    private $_port = 0;
    private $_isConnected = false;
    private $_siteId = 'repositorios';

    // CONSTRUCTOR
    public function __construct($ip, $port = '', $user, $password)
    {
        try {
            $this->_user = $user;
            $this->_password = $password;
            $this->_ip = trim($ip);
            $this->_port = (!empty($port))?':'.$port:'';
            $this->_url = 'http://'.$this->_ip.$this->_port.'/alfresco/api/-default-/public/cmis/versions/1.1/atom/';
            $this->_returnDataType = self::NORMAL;
            $this->_clientCMIS = new \CMISService($this->_url, $this->_user, $this->_password);
            $this->_isConnected = is_object($this->_clientCMIS);
        } catch (\Exception $e) {
            $this->lanzarError($e, '', ' - __construct - ', '');
        }
    }

    public function isConnected()
    {
        return $this->_isConnected;
    }

    private function obtainConstant($constant)
    {
        try {
            $tmp = 'self::$constant';
            eval("\$constant = \"$tmp\";");
            $constant = constant($constant);
        } catch (\Exception $ex) {
            $constant = $this->_returnDataType;
        }
        return $constant;
    }

    // ESTABLECER EL TIPO DE REGRESO DE DATOS
    public function setReturnDataType($returnType)
    {
        $this->_returnDataType = $this->obtainConstant(strtoupper(trim($returnType)));
    }

    // OBTENER EL TIPO DE REGRESO DE DATOS
    public function getReturnDataType()
    {
        return $this->_returnDataType;
    }

    public function createNode($path, $name, $title, $description = '')
    {
        $response = array('success' => true, 'create' => false, 'error' => 'Unknown');
        $properties = array('title' => $title, 'summary' => $description);
        $currentNode = null;
        $newNode = null;
        $errorPath = $path;

        try {
            $currentNode = $this->_clientCMIS->getObjectByPath('/'.$path);

            if (!empty($currentNode->id)) {
                $errorPath .= '/'.$name;
                $newNode = $this->_clientCMIS->createFolder($currentNode->id, utf8_encode($name), $properties);

                if (!empty($newNode->id)) {
                    $this->_clientCMIS->updateProperties($newNode->id, $properties);
                    $response['create'] = true;
                    $response['error'] = '';
                }
            }
        } catch(\Exception $e) {
            $this->lanzarError($e, $this->_user, ' - createNode - ', $errorPath);
        }

        $currentNode = null;
        $newNode = null;

        return $this->parseResponse($response);
    }

    public function deleteFile($path, $file)
    {
        $responseTmp = array('success' => true, 'deleted' => false, 'error' => 'Unknown');
        $deleted = false;
        $response = null;

        try {
            $node = $this->_clientCMIS->getObjectByPath('/'.$path);
            $childrens = $this->_clientCMIS->getChildren($node->id);

            if ($childrens->numItems > 0) {
                foreach ($childrens->objectList as $children) {
                    if (
                        $children->properties['cmis:baseTypeId'] == "cmis:document" &&
                        $children->properties['cmis:name'] == $file
                    ) {

                    $this->_clientCMIS->deleteObject($children->id);
                    $deleted = $responseTmp['deleted'] = true;
                    $responseTmp['error'] = '';
                    break;
                    } else {
                    $responseTmp['error'] = $this->parseErrorCodeCMIS(404, $file);
                    $deleted = $responseTmp['deleted'] = true;
                    }
                }
            } else {
                $responseTmp['error'] = $this->parseErrorCodeCMIS(404, $file);
                $deleted = $responseTmp['deleted'] = true;
            }
        } catch(\Exception $e) {
            $this->lanzarError($e, $this->_user, ' - deleteFile - ', $path.'/'.$file);
        }

        $response = ($this->getReturnDataType() == self::JSON)?$responseTmp:$deleted;
        $clientCMIS = null;

        return $this->parseResponse($response);
    }

    public function findFileJur($path, $file)
    {
        $fields = "";
        $urlAdicion = "";
        $ruta = "";
        $urlAdicion = "path?path=/".$file;

        if (!empty($path)) {
            $urlAdicion = "path?path=/".urlencode($path).'/'.urlencode($file);
        }

        // $cachorro->setU("Hunky");
        // // Y mostrarlo con getNombre():
        // echo $cachorro->getUrl(); 

        // $childrens = $this->_clientCMIS->getChildren($node->id);
        $url = $this->_clientCMIS->getUrl();
        // $url = $this->_clientCMIS->url;
        $busqueda = curl_init($url.$urlAdicion);
        curl_setopt($busqueda, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($busqueda, CURLOPT_USERPWD, $this->_user.':'.$this->_password);
        $rutaObj = curl_exec($busqueda);
        $code = curl_getinfo($busqueda, CURLINFO_HTTP_CODE);

        if($code == 404) {
            curl_close($busqueda);
            return;
        }

        curl_close($busqueda);
        $xml = (\CMISRepositoryWrapper :: extractObject($rutaObj));
        $idObject = "";
        $cl = "id";
        foreach($xml as $clave => $valor){
            if($clave == $cl){
                $idObject = $valor;
            }
        }
        $ruta = " AND IN_FOLDER('".$idObject."')";

        $query = '';
        $url = $this->_clientCMIS->getUrl() . "content?id=" .$idObject;
        $s = curl_init($url);
        curl_setopt($s, CURLOPT_USERPWD, $this->_user.':'.$this->_password);
        curl_setopt($s, CURLOPT_HEADER, false);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);

        $resultado = curl_exec($s);
        curl_close($s);

        if($resultado!=''){
            $response['total'] =1;
            $response['success'] = true;
            $response['error'] = '';
            $response['datos'] = $this->parseResponse($resultado);
        }else{
            $response['total'] = 0;
            $response['success'] = false;
            $response['error'] = '';
            $response['datos'] = '';
        }

        return $response;
    }

    public function fileExists($path, $file)
    {
        $found = false;
        $query = '';

        try {
            if (!empty($path)) {
                $path = $this->_clientCMIS->getObjectByPath('/'.$path);
                $path = ' AND IN_FOLDER(\''.$path->id.'\')';
            }

            $query = 'SELECT * FROM cmis:document WHERE cmis:name = \''.$file.'\''.$path;
            $documents = $this->_clientCMIS->query($query);

            if ($documents->numItems > 0) {
                $found = true;
            }
        } catch (\Exception $e) {
            $this->lanzarError($e, $this->_user, ' - fileExists - ', $path);
        }

        return $found;
    }

    public function getContent($path = '')
    {
        $response = array('success' => true, 'total' => 0, 'data' => array(), 'error' => 'unknown');
        $indexFolder = array('type', 'path', 'name', 'creationDate', 'lastModifiedBy',
                            'createdBy', 'id', 'baseTypeId', 'lastModificationDate', 'parentId'
                        );
        $indexFile = array(
                        'isLatestMajorVersion', 'contentStreamLength', 'contentStreamId', 'type', 'name',
                        'contentStreamMimeType', 'versionSeriesId', 'creationDate', 'versionLabel', 'isLatestVersion',
                        'isVersionSeriesCheckedOut', 'lastModifiedBy', 'createdBy', 'id', 'isImmutable',
                        'isMajorVersion', 'baseTypeId', 'contentStreamFileName', 'lastModificationDate'
                    );
        $currentNode = null;

        try {
            $currentNode = $this->_clientCMIS->getObjectByPath('/'.$path);

            if (!empty($currentNode->id)) {
                $childrens = $this->_clientCMIS->getChildren($currentNode->id);

                foreach ($childrens->objectList as $children) {
                    $content = null;
                    $type = '';
                    $url = '';

                    switch ($children->properties['cmis:baseTypeId']) {
                    case 'cmis:document':
                        $data = explode(':', $children->uuid);
                        $type = 'file';
                        $url = 'http://'.$this->_ip.$this->_port;
                        $url .= '/share/proxy/alfresco/api/node/content/workspace/SpacesStore/'.$data[2].'/';
                        $url .= $children->properties['cmis:name'];

                        break;

                    case 'cmis:folder':
                        $type = 'folder';
                        break;

                    default:
                        $type = 'unknown';
                        break;
                    }

                    $content = array(
                        'id' => $children->properties['cmis:objectId'],
                        'path' => (isset($children->properties['cmis:path']))?$children->properties['cmis:path']:'',
                        'name' => $children->properties['cmis:name'],
                        'url' => $url,
                        'type' => $type,
                        'size' => $children->properties['cmis:contentStreamLength'],
                        'createdBy' => $children->properties['cmis:createdBy'],
                        'creationDate' => $children->properties['cmis:creationDate'],
                        'lastModifiedBy' => $children->properties['cmis:lastModifiedBy'],
                        'lastModificationDate' => $children->properties['cmis:lastModificationDate']
                    );

                    array_push($response['data'], $content);
                }

                $response['total'] = sizeof($response['data']);
                $response['error'] = '';
            }
        } catch (\Exception $e) {
            $this->lanzarError($e, $this->_user, ' - getContent - ', $path);
        }

        $currentNode = null;

        return $this->parseResponse($response);
    }

    public function nodeExists($path)
    {
        $responseTmp = array('success' => true, 'exists' => false, 'error' => 'Unknown');
        $currentNode = null;
        $exists = false;
        $response = null;

        try {
            $currentNode = $this->_clientCMIS->getObjectByPath('/'.$path);
            
            if (!empty($currentNode->id)) {
                $exists = $responseTmp['exists'] = true;
                $responseTmp['error'] = '';
            }
        } catch (\Exception $e) {
            $this->lanzarError($e, $this->_user, ' - nodeExists - ', $path);
        }

        $response = ($this->getReturnDataType() == self::JSON)?$responseTmp:$exists;
        $currentNode = null;

        return $this->parseResponse($response);
    }

    public function uploadFile($path, $files, $overWrite = true)
    {
        $respuesta = null;
        $respuesta['loaded'] = false;
        $respuesta['mensaje'] = '';

        try {
            $url = 'http://'.$this->_ip.$this->_port.'/alfresco/api/-default-/public/cmis/versions/1.1/atom/';
            foreach ($files as $file) {
                $urlAdicion = "";
                $alfrescoPath=$path;
                $pathAndFile = $file['tmp_name'];
                if($fp = @fopen($pathAndFile, "r")) {
                    if($content = @fread($fp,@filesize($pathAndFile))) {
                        @fclose($fp);
                        $finfo = finfo_open(FILEINFO_MIME);
                        $filetype = finfo_file($finfo, $pathAndFile);
                        finfo_close($finfo);
                        $properties = array ();
                        $options = array('cmis:objectTypeId'=>'D:ex:document');
                        $respuesta = array();

                        $urlAdicion = $urlAdicion = "path?path=/".urlencode($alfrescoPath);

                        $busqueda = curl_init($url.$urlAdicion);
                        curl_setopt($busqueda, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($busqueda, CURLOPT_USERPWD, $this->_user.':'.$this->_password);
                        $rutaObj = curl_exec($busqueda);
                        curl_close($busqueda);

                        $xml = (\CMISRepositoryWrapper :: extractObject($rutaObj));
                        $id = "";
                        $cl = "id";
                        foreach($xml as $clave => $valor){
                            if($clave == $cl) {
                                $id = $valor;
                                break;
                            }
                        }
                        if($id) {
                            @$this->deleteFile($alfrescoPath, $file['name']);
                            if($this->_clientCMIS->postObject(
                                    $id,
                                    ($file['name']),
                                    "cmis:document",
                                    $properties,
                                    $content,
                                    $filetype,
                                    $options
                                )
                            ) {
                                $respuesta['loaded'] = true;
                                $respuesta['mensaje'] = "OK";
                            } else {
                                $respuesta['loaded'] = false;
                                $respuesta['mensaje'] = "failed";
                            }
                        } else {
                            $respuesta['loaded'] = false;
                            $respuesta['mensaje'] = "failed";
                        }
                    } else {
                        $respuesta['loaded'] = false;
                        $respuesta['mensaje'] = "failed";
                    }
                } else {
                    $respuesta['loaded'] = false;
                    $respuesta['mensaje'] = "failed";
                }
            }
        } catch(\Exception $ex) {
            $respuesta['loaded'] = false;
            $respuesta['mensaje'] = "failed" .$ex;
        }
        return $respuesta;
    }

      

    private function parseResponse($response)
    {
        try {
            if ($this->getReturnDataType() == self::JSON) {
                $response = json_encode($response);
            }
        } catch (\Exception $e) {    
            $respuesta['loaded'] = false;
            $respuesta['mensaje'] = "failed" .$e;     
        }

        return $response;
    }

    private function parseErrorCodeCMIS($errorCode, $object = '', $fullError = null)
    {
        $errorDescription = 'unknown';

        try {
            $object = (!empty($object))?' "'.$object.'" ':' ';

            switch ($errorCode) {
                case 404:
                    $errorDescription = 'File or folder'.$object.'not found.';
                    break;

                case 409:
                    $errorDescription = 'File or folder'.$object.'already exists.';
                    break;

                default:
                    $errorDescription = 'Error '.$errorCode.' in '.$object.' - '.($fullError != null)?json_encode($fullError):array();
                    break;
            }
        } catch (\Exception $e) {  
            $respuesta['loaded'] = false;
            $respuesta['mensaje'] = "failed" .$e;       
        }

        return $errorDescription;
    }

    public function lanzarError($e, $usuario, $metodo, $errorPath) {
        $log = new \Logger();
        $error = $this->parseErrorCodeCMIS($e->getCode(), $errorPath, $e);
        $log->warning($usuario.$metodo.$error);
        $response['success'] = false;
        $response['error'] = $error;

        $log = null;
        return $this->parseResponse($response);
    }
}
