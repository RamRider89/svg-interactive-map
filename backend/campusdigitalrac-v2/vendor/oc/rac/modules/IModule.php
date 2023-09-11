<?php

namespace Coppel\RAC\Modules;

/**
 * Interfaz que describe el comportamiento esperado del Modulo de RAC.
 * Debe ser capas de registrar los namespace de sus controladores y modelos,
 * registrar las rutas asociadas a sus controladores y dar de alta servicio que necesite la aplicación.
 */
interface IModule
{
    /**
     * Registra los namespace de la aplicación.
     *
     * @param Phalcon\Autoload\Loader $loader Instancia para registrar los namespace.
     * @return void
     */
    function registerLoader($loader);

    /**
     * Retorna un arreglo con las colecciones (Phalcon\Mvc\Micro\Collection) del modulo.
     *
     * @return void
     */
    function getCollections();

    /**
     * Registra en el inyector de dependencias de Phalcon (Phalcon\DI\DI) los servicios que necesita la aplicación.
     *
     * @return void
     */
    function registerServices();
}
