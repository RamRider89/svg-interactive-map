<?php

namespace Coppel\RAC\Services;

use Exception;
use Throwable;
use Phalcon\DI\DI;
use Phalcon\Http\Request as PhalconRequest;
use Httpful\Request;
use GuzzleHttp as Guzzle;

/**
 * Clase que gestiona la interacción con el Servicio de Autenticación interno (sso.coppel.io).
 */
class Authentication
{
    /**
     * @var string|null $token Valor del token enviado por el cliente para autenticarse.
     */
    private ?string $token;

    /**
     * @var string|null $url EndPoint obtenido del archivo de configuración para autenticarse.
     */
    private ?string $url;

    /**
     * @var float SERVICE_TIMEOUT Tiempo de espera al Servicio de Autenticación
     */
    private const SERVICE_TIMEOUT = 15.0;

    /**
     * Constructor predeterminado.
     */
    public function __construct()
    {
        $config = DI::getDefault()->get('config');

        $this->url = $config->auth;
        $this->token = (new PhalconRequest())->getHeader('Authorization');

        $this->authService = new Guzzle\Client([
            'base_uri' => $this->url,
            'timeout' => self::SERVICE_TIMEOUT,
        ]);
    }

    /**
     * Método para obtener la información de la sesión actual del servicio de autenticación.
     *
     * @deprecated 2.0.0 Usar el método getTokenInfo.
     * @return mixed Objeto con la información de sesión, `false` en caso de error.
     */
    public function obtenerSesion()
    {
        $url = $this->url;
        $respuesta = null;
        $response = false;

        $url = str_replace('/verify', '/me', $url);

        $respuesta = Request::post($url)
            ->sendsJson()
            ->addHeader('Authorization', $this->token)
            ->send();

        if (!$respuesta->hasErrors() && $respuesta->hasBody()) {
            $response = $respuesta->body->data;
        }

        return $response;
    }

    /**
     * Método que verifica el estatus de sesión del servicio del servicio de autenticación.
     *
     * @deprecated 2.0.0 Usar el método verifyToken
     * @return mixed Objeto con la información del estatus de sesión, `false` en caso de error.
     */
    public function verificarSesion()
    {
        $respuesta = null;
        $response = false;

        $respuesta = Request::post($this->url)
            ->sendsJson()
            ->addHeader('Authorization', $this->token)
            ->send();

        if (!$respuesta->hasErrors() && $respuesta->hasBody()) {
            $response = $respuesta->body->data;
        }

        return $response;
    }

    /**
     * Verifica el estatus del Token en el Servicio de Autenticación.
     *
     * @since 2.0.0
     * @return boolean Retorna `true` para un Token válido, `false` en caso contrario.
     */
    public function verifyToken(): bool
    {
        $response = null;

        try {
            $response = $this->authService->post('api/v1/verify', [
                Guzzle\RequestOptions::HEADERS => [
                'Authorization' => $this->token
            ]]);
        } catch (Guzzle\Exception\ClientException $ex) {
            $this->handleException($ex);
        }

        return ($response && $response->getStatusCode() === 200);
    }

    /**
     * Obtiene la información contenida en el Token del Servicio de Autenticación.
     *
     * @since 2.0.0
     * @return mixed `Array` con la información general contenida en el Token, `false` en caso de Token inválido.
     */
    public function getTokenInfo(): mixed
    {
        $response = null;

        try {
            $response = $this->authService->get('api/v2/me', [
                Guzzle\RequestOptions::HEADERS => [
                'Authorization' => $this->token
            ]]);
        } catch (Guzzle\Exception\ClientException $ex) {
            $this->handleException($ex);
        }

        if ($response && $response->getStatusCode() === 200) {
            $body = json_decode($response->getBody(), true);

            if (!($body && isset($body['data']))) {
                throw new Exception('El servicio de autenticación, no contiene la respuesta esperada.', 500);
            }

            return $body['data'];
        }

        return false;
    }

    /**
     * Método para manejar una excepción de una petición al Servicio de Autenticación.
     *
     * @param Throwable $ex Excepción a controlar.
     * @return void
     */
    private function handleException(Throwable $ex): void
    {
        $response = $ex->getResponse();

        if ($ex->getCode() === 404 ||
            ($response->hasHeader('content-type') && stripos($response->getHeader('content-type')[0], 'application/json') === false)) {
            throw $ex;
        }
    }

    /**
     * Obtiene el valor de Token del Servicio de Autenticación.
     *
     * @return string|null Token del Servicio de Autenticación.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Obtiene el valor de token.
     *
     * @return string Token.
     */

    /**
     * Establece el valor de Token del Servicio de Autenticación.
     *
     * @param string $token Token del Servicio de Autenticación.
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Obtiene el valor de URL del Servicio de Autenticación.
     *
     * @return string|null URL del Servicio de Autenticación.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Establece el valor de URL del Servicio de Autenticación.
     *
     * @param string $url URL del Servicio de Autenticación.
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
