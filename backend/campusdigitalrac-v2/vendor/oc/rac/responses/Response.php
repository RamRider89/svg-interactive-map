<?php

namespace Coppel\RAC\Responses;

use Phalcon\DI\DI;
use Phalcon\DI\Injectable;

/**
 * Clase base de respuesta del framework.
 */
class Response extends Injectable
{
    function __construct()
    {
        $di = DI::getDefault();
        $this->setDI($di);
    }
}
