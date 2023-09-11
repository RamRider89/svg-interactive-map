<?php

namespace Coppel\RAC\Responses;

/**
 * Clase para tratar la respuesta final del framework verificando y estableciendo
 * los elementos que se regresarán al cliente, además de poner las llaves de
 * respuesta en formato JSON correcto.
 */
class JSONResponse extends Response
{
    protected $envelope = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function send($data = null, $error = false)
    {
        $request = $this->di->get('request');
        $response = $this->di->get('response');

        if ($request->get('envelope') === 'false') {
            $this->envelope = false;
        }

        if ($this->envelope) {
            $message = ['meta' => [
                'status' => $error ? 'ERROR' : 'SUCCESS',
                'count' => count((array)$data)
            ]];

            if ($error && isset($data['internalCode'])) {
                $message['meta']['internalCode'] = $data['internalCode'];
            }

            $message['data'] = $data;
            $data = $message;
        }

        if ($data !== null) {
            $response->setJsonContent($data);
        }

        $response->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
            ->setHeader('Access-Control-Allow-Credentials', true)
            ->send();

        return $this;
    }

    public function useEnvelope($envelope)
    {
        $this->envelope = (bool)$envelope;
        return $this;
    }
}
