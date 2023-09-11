<?php

namespace Coppel\RAC\Exceptions;

use Exception;
use Phalcon\DI\DI;
use Coppel\RAC\Responses\JSONResponse;

/**
 * Excepción personalizada del framework, el cual está preparado
 * para manejar este tipo de excepción y dar un formato de salida
 * de acuerdo al estándar aunque se haya lanzado esta excepción.
 */
class HTTPException extends Exception
{
    /**
     * @var string $codeDescription Descripción de la respuesta
     */
    public $codeDescription;

    /**
     * @var string $devMessage Mensaje dirigido a quien de mantenimiento o atienda la incidencia
     */
    public $devMessage;

    /**
     * @var string $more Mensaje dirigido a quien atiende la excepción con información adicional
     */
    public $more;

    /**
     * Constructor de la clase HTTPException.
     *
     * @param string $message Mensaje para mostrar a un usuario final
     * @param int $code El código HTTP de respuesta
     * @param array $errorArray Un arreglo con información de la excepción
     */
    public function __construct($message, $code, $errorArray = [])
    {
        $this->message = $message;
        $this->devMessage = array_key_exists('dev', $errorArray) ? $errorArray['dev'] : null;
        $this->more = array_key_exists('more', $errorArray) ? $errorArray['more'] : null;
        $this->from = array_key_exists('from', $errorArray) ? $errorArray['from'] : null;
        $this->internalCode = array_key_exists('internalCode', $errorArray) ? $errorArray['internalCode'] : null;
        $this->code = $code;
        $this->codeDescription = $this->getResponseDescription($code);
    }

    /**
     * Envía la respuesta al cliente. El código HTTP y la respuesta del servidor
     * son los que el desarrollador haya determinado al lanzar la excepción.
     *
     * @return void
     */
    public function send()
    {
        $di = DI::getDefault();
        $request = $di->get('request');
        $response = $di->get('response');

        if ($request->get('suppress_response_codes')) {
            $response->setStatusCode('200', $this->getResponseDescription(200));
        } else {
            $response->setStatusCode($this->getCode(), $this->codeDescription);
        }

        $error = [
            'errorCode' => $this->getCode(),
            'userMessage' => $this->getMessage(),
            'internalCode' => $this->internalCode
        ];

        if (getenv('PHP_ENV') !== 'production') {
            $error['devMessage'] = $this->devMessage;
            $error['more'] = $this->more;
            $error['file'] = $this->getFile();
            $error['line'] = $this->getLine();
            $error['from'] = $this->from;
        }

        (new JSONResponse())->send($error, true);
    }

    /**
     * Retorna una descripción para el código HTTP al enviar la respuesta.
     *
     * @param int $code El código HTTP a describir.
     * @return string
     */
    protected function getResponseDescription($code)
    {
        $codes = [
            // 1xx Informational
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',

            // 2xx Succesful
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',

            // 3xx Redirection
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',

            // 4xx Client Error
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => "I'm a Teapot",
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',

            // 5xx Server Error
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Gateway',
            511 => 'Network Authentication Required'
        ];

        return isset($codes[$code]) ? $codes[$code] : 'Unknown Status Code';
    }
}
