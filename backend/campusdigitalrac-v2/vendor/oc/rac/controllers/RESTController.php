<?php

namespace Coppel\RAC\Controllers;

use Phalcon\Mvc\Controller;
use Coppel\RAC\Exceptions\HTTPException;

/**
 * Controlador base del framework. Todos los controladores deben heredarlo.
 * Extiende el controlador base de Phalcon. Al hacer esta herencia,
 * todos los controladores tendrán acceso a las peticiones HTTP recibidas,
 * además de pasar a usar el método onConstruct en lugar de __construct.
 */
class RESTController extends Controller
{
    /**
     * Método de respuesta de los métodos en los controladores de la aplicación.
     *
     * @deprecated 2.0.0 Retornar directamente el valor.
     * @param array|null $recordsArray Un arreglo (asociativo o no) con la respuesta del controlador.
     * @return array
     */
    protected function respond(?array $recordsArray = null)
    {
        if (!is_array($recordsArray)) {
            throw new HTTPException(
                'Ocurrió un error mientras se obtenían los datos de retorno.',
                500, [
                    'dev' => 'El objeto de retorno siempre debería ser un arreglo.',
                    'internalCode' => 'A1000',
                    'more' => 'Cambie los datos de retorno para ser un arreglo.',
                    'from' => 'RAC'
                ]
            );
        }

        return $recordsArray;
    }
}
