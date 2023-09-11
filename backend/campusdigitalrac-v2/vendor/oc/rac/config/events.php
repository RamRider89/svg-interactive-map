<?php

use Coppel\RAC\Exceptions\HTTPException;
use Coppel\RAC\Responses\JSONResponse;

return [
    'exceptionHandler' => function($exception)
    {
        if ($exception instanceof HTTPException) {
            $exception->send();
        } else {
            (new HTTPException(
                'No es posible completar su solicitud, intente de nuevo o contacte al administrador.',
                500, [
                    'dev' => $exception->getMessage(),
                    'internalCode' => 'RACIE1000',
                    'more' => 'Verificar que tipo de excepción se está lanzando y controlarla.',
                    'from' => 'RAC'
                ]
            ))->send();
        }

        error_log($exception);
    },
    'micro:beforeHandleRoute' => function($event, $app)
    {

    },
    'micro:beforeExecuteRoute' => function($event, $app)
    {
        if ($app->isTestMode || $app->request->isOptions()) {
            return;
        }

        $di = $app->getDI();
        $config = $di->get('config');

        if (isset($config->uriException)) {
            if (is_array($config->uriException)) {
                $baseUri = $di->get('router')->getMatchedRoute()->getPattern();

                if (in_array($baseUri, $config->uriException, true)) {
                    return;
                }
            } else {
                throw new HTTPException(
                    'El listado de excepciones de URIs no tiene el formato correcto.',
                    500, [
                        'dev' => 'El listado URIs debe ser un array.',
                        'internalCode' => 'UEL1000',
                        'more' => 'Las URIs deben iniciar en /, tener el prefijo y la URI de su ruta.',
                        'from' => 'RAC'
                    ]
                );
            }
        }

        $auth = $di->get('auth');
        $token = $auth->getToken();

        if (strlen($token) === 0) {
            throw new HTTPException(
                'Usted no está autorizado para acceder a este recurso.',
                401, [
                    'dev' => 'Está accediendo a la API sin un token.',
                    'internalCode' => 'FDS1000',
                    'more' => 'Verificar que la petición tenga un token del servicio de autenticación válido.',
                    'from' => 'RAC'
                ]
            );
        }

        try {
            if ($auth->verifyToken()) {
                return;
            }
        } catch (Exception $ex) {
            throw new HTTPException(
                'Su solicitud no puede ser completada, intente de nuevo o contacte al administrador.',
                500, [
                    'dev' => $ex->getMessage(),
                    'internalCode' => 'SWE1000',
                    'more' => 'Verificar el estado del servicio de autenticación.',
                    'from' => 'RAC'
                ]
            );
        }

        throw new HTTPException(
            'Usted no está autorizado para acceder a este recurso.',
            401, [
                'dev' => 'Está accediendo a la API con un token inválido.',
                'internalCode' => 'FDS2000',
                'more' => 'Verificar que la petición tenga un token del servicio de autenticación válido.',
                'from' => 'RAC'
            ]
        );
    },
    'micro:beforeNotFound' => function($event, $app)
    {
        throw new HTTPException(
            'Not found',
            404, [
                'dev' => 'La ruta no fue encontrada en el servidor.',
                'internalCode' => 'NF1000',
                'more' => 'Verifique que la URI esté bien escrita.',
                'from' => 'RAC'
            ]
        );
    },
    'micro:afterHandleRoute' => function($event, $app)
    {
        $response = new JSONResponse();

        if ($app->request->isOptions()) {
            $response->useEnvelope(false)->send();
        } else {
            $response->send($app->getReturnedValue());
        }
    }
];
