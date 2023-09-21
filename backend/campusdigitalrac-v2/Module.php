<?php

namespace Coppel\RAC\Modules;

use PDO;
use Phalcon\DI\DI;
use Phalcon\Mvc\Micro\Collection;

class Module implements IModule
{
    const UNDIVAGO = array(
        'ct' => "OQXdDYx5HxTcdTr39LyhBU2rgJDImeJ65v4t+x5Ki+k=",
        "iv" => "42cf781b32b4c11bf39b05d2be4510da",
        "s" => "00c178d7d396b8c1"
    );

    public function registerLoader($loader)
    {
        $loader->setNamespaces([
            'Coppel\Campusdigitalrac\Controllers' => __DIR__ . '/controllers/',
            'Coppel\Campusdigitalrac\Models' => __DIR__ . '/models/',
            'Coppel\Campusdigitalrac\Alfresco' => __DIR__ . '/alfresco/',
            'Coppel\Campusdigitalrac\Utilerias' => __DIR__ . '/utilerias/',
            'Coppel\Campusdigitalrac\Mail' => __DIR__ . '/mail/',
            'Coppel\Campusdigitalrac\Libs' => __DIR__ . '/libs/'
        ], true);
    }

    public function getCollections()
    {
        $ejemplo = new Collection();

        $ejemplo->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\ApiController')
            ->setLazy(true);
        $ejemplo->get('/ejemplo', 'holaMundo');

        // CATALOGO LOGS API LOGS SERVICES
        $logs = new Collection();
        $logs->setPrefix('/api')
        ->setHandler('\Coppel\Campusdigitalrac\Controllers\logsController')
        ->setLazy(true);
        $logs->post('/logs', 'logs');
        $logs->get('/getlocaliplogs', 'getIPCliente');

        // CATALOGO POSICION
        $posicion = new Collection();
        $posicion->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\PosicionController')
            ->setLazy(true);
        $posicion->get('/getposicion/{idPosicion}', 'getPosicion');
        $posicion->post('/getposicionbyname', 'getPosicionByName');
        $posicion->post('/getposiciones', 'getPosiciones');
        $posicion->post('/codigoconfirmacion', 'setCodigoConfirmacion');
        $posicion->post('/setposition', 'setPosicion');


        // CATALOGO GRUPOS TRABAJO
        $grupos = new Collection();
        $grupos->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\GruposTrabajoController')
            ->setLazy(true);
        $grupos->get('/gettipogrupos/{idTipo}', 'getTipoGruposTrabajo');
        $grupos->post('/getgrupostrabajo', 'getGruposTrabajo');

        
        // CATALOGO CONTACTOS
        $contactos = new Collection();
        $contactos->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\ContactosController')
            ->setLazy(true);
        $contactos->post('/getcontactos', 'getContactos');

        // CATALOGO EMPLEADO
        $empleado = new Collection();
        $empleado->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\EmpleadoController')
            ->setLazy(true);
        $empleado->post('/getempleado', 'getEmpleado');
        $empleado->post('/getempleadocompleto', 'getEmpleadoCompleto');
        $empleado->get('/lineadenuncia', 'obtenerLineaDenuncia');
        $empleado->get('/enviarcorreo', 'enviarCorreo');
        $empleado->get('/gettipotrabajo/{idTipo}', 'getTipoTrabajo');
        $empleado->post('/getlidercentro', 'getLiderCentro');

        // CATALOGO USER POSITION
        $userPosition = new Collection();
        $userPosition->setPrefix('/api')
            ->setHandler('\Coppel\Campusdigitalrac\Controllers\UserPosicionController')
            ->setLazy(true);
        $userPosition->get('/codigoconfirm', 'getCodigoConfirmacion');
        $userPosition->post('/enviarcorreoconfirm', 'enviarCorreoConfirmacion');
        $userPosition->post('/setuserposition', 'setUserPosicion');

        return [
            $ejemplo,
            $logs,
            $posicion,
            $grupos,
            $contactos,
            $empleado,
            $userPosition
        ];
    }

    public function registerServices()
    {
        $di = DI::getDefault();
        $config = (object)$this->Decrypt($this->Decrypt('', json_encode(self::UNDIVAGO)), json_encode($di->get('config')->dbConstrunet));

        // CONSTRUNET
        $di->set('dbConstrunet', function () use ($config) {
            return new PDO(
                "sqlsrv:Server={$config->host};Database={$config->dbname};",
                $config->username,
                $config->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        });

        // LOGGER
        $di->set('logger', function () {
            return new \Katzgrau\KLogger\Logger('logs');
        });


        // CONSTRUNET DESARROLLO
        $di_des = DI::getDefault();
        $di_des->set('ConstruNet_des', function () {
            return new PDO(
                "sqlsrv:Server=10.44.74.124;Database=ConstruNet_des;",
                'sysredrabbit',
                'b003dae85f0930a8b211155ed8c20b1f',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        });

        // PERSONAL DESARROLLO
        $di_personal = DI::getDefault();
        $di_personal->set('Personal_des', function () {
            return new PDO(
                "sqlsrv:Server=10.49.123.215;Database=Personal;",
                'syspruebasadmon',
                '1299e3097',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        });

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
}
