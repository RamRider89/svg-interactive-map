<?php

namespace Coppel\RAC;

use Phalcon\Autoload\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\CollectionInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\DI\FactoryDefault;
use Coppel\RAC\Modules\Module;
use Coppel\RAC\Exceptions\HTTPException;

/**
 * Clase que gestiona el funcionamiento de RAC, combinando la carga de las clases propias del
 * framework con las de Phalcon.
 */
class Application extends Micro
{
    /**
     * Contiene la dirección raíz de la aplicación.
     *
     * @var string
     */
    private string $ROOT_PROJECT_PATH;

    /**
     * Carga los namespace de la aplicación.
     *
     * @var Phalcon\Autoload\Loader
     */
    private $loader;

     /**
      * Inyector de dependencias (servicios) de Phalcon.
      *
      * @var Phalcon\DI\FactoryDefault
      */
    private $di;

    /**
     * Registra los eventos contenidos en $events.
     *
     * @var Phalcon\Events\Manager
     */
    protected $eventsManager;

    /**
     * Contiene los eventos de la aplicación, como antes de no encontrar una ruta y antes de ejecutar una encontrada.
     *
     * @var array
     */
    private array $events;

    /**
     * Contiene servicios propios del framework, como Authentication.
     *
     * @var array
     */
    private array $services;

    /**
     * Indica si la aplicación debe correr en modo de prueba (sin validación de token).
     *
     * @var boolean
     */
    private bool $isTestMode = false;

    /**
     * Constructor predeterminado de la clase Application.
     *
     * @param string $config Una cadena para una posible configuración a futuro.
     */
    public function __construct(string $config)
    {
        $this->ROOT_PROJECT_PATH = dirname(__DIR__, 3);

        $this->registerLoader();

        $this->events = require_once __DIR__ . '/config/events.php';

        $this->services = require_once __DIR__ . '/config/services.php';

        $this->registerExceptionHandler();

        $this->registerEvents();

        $this->registerServices();

        $this->registerConfig($config);
    }

    /**
     * Evalúa si una cadena JSON está bien formada.
     *
     * @param string $json La cadena JSON a evaluar.
     * @return boolean Si el JSON está bien formado o no.
     */
    private function validJson($json)
    {
        json_decode($json);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Registra el archivo de configuración en el Inyector de dependencias.
     *
     * @param string $config Una cadena para una configuración a futuro.
     * @return void
     */
    private function registerConfig($config): void
    {
        $file = "{$this->ROOT_PROJECT_PATH}/config.json";

        if (!file_exists($file)) {
            throw new HTTPException(
                'Ocurrió un error al cargar uno de los módulos de configuración.',
                500, [
                    'dev' => 'No es posible desplegar el módulo debido a que no se encuentra el archivo config.json.',
                    'internalCode' => 'NF2000',
                    'more' => 'Se buscaba el archivo config.json.',
                    'from' => 'RAC'
                ]
            );
        }

        $jsonConfig = file_get_contents($file);

        if (!$this->validJson($jsonConfig)) {
            throw new HTTPException(
                'Un archivo de configuración no está bien formado.',
                500, [
                    'dev' => 'No es posible desplegar el módulo debido a que el JSON del archivo config.json está mal formado.',
                    'internalCode' => 'NF3000',
                    'more' => 'Verificar el formato del archivo config.json.',
                    'from' => 'RAC'
                ]
            );
        }

        $config = json_decode($jsonConfig);

        $this->di->set('config', function () use ($config)
        {
            return $config;
        });
    }

    /**
     * Registra los namespace de RAC.
     *
     * @return void
     */
    private function registerLoader(): void
    {
        $this->loader = new Loader();

        $this->loader->setNamespaces([
            'Coppel\RAC' => __DIR__ . '/',
            'Coppel\RAC\Controllers' => __DIR__ . '/controllers/',
            'Coppel\RAC\Responses' => __DIR__ . '/responses/',
            'Coppel\RAC\Modules' => __DIR__ . '/modules/',
            'Coppel\RAC\Exceptions' => __DIR__ . '/exceptions/',
            'Coppel\RAC\Services' => __DIR__ . '/services/',
        ])->register();
    }

    /**
     * Asigna el manejador de excepciones por omisió para excepciones no controladas.
     *
     * @return void
     */
    private function registerExceptionHandler(): void
    {
        set_exception_handler($this->events['exceptionHandler']);
    }

    /**
     * Registra los eventos en el flujo de rutas de la aplicación.
     *
     * @return void
     */
    public function registerEvents(): void
    {
        $this->eventsManager = new EventsManager();

        $this->eventsManager->attach('micro:beforeHandleRoute', $this->events['micro:beforeHandleRoute']);
        $this->eventsManager->attach('micro:beforeExecuteRoute', $this->events['micro:beforeExecuteRoute']);
        $this->eventsManager->attach('micro:beforeNotFound', $this->events['micro:beforeNotFound']);
        $this->eventsManager->attach('micro:afterHandleRoute', $this->events['micro:afterHandleRoute']);
    }

    /**
     * Registra el módulo de la aplicación. Este debe debe cumplir con la interfaz IModule y retornar
     * las colecciones (conjunto de rutas referenciando a métodos en controladores), de lo contrario no se podrán registrar.
     *
     * @return void
     */
    private function loadModule(): void
    {
        $file = "{$this->ROOT_PROJECT_PATH}/Module.php";

        if (!file_exists($file)) {
            throw new HTTPException(
                'Ocurrió un error al cargar uno de los módulos requeridos.',
                500, [
                    'dev' => 'No es posible registrar el módulo debido a que no se encuentra el archivo Module.php.',
                    'internalCode' => 'NF4000',
                    'more' => "Se buscaba el archivo $file.",
                    'from' => 'RAC'
                ]
            );
        }

        require_once $file;

        $module = new Module();

        $module->registerLoader($this->loader);
        $module->registerServices();
        $collections = $module->getCollections();

        if (!is_array($collections)) {
            $collections = [$collections];
        }

        foreach ($collections as $collection) {
            if ($collection instanceof CollectionInterface) {
                $this->mount($collection);
            }
        }
    }

    /**
     * Registra los servicios en el Inyector de dependencias de Phalcon.
     *
     * @return void
     */
    private function registerServices(): void
    {
        $this->di = new FactoryDefault();

        foreach ($this->services as $key => $service) {
            if (!$this->di->has($key)) {
                $this->di->set($key, $service, true);
            }
        }
    }

    /**
     * Configura en la ruta raíz un resumen de las rutas de la aplicación y servicios del inyector de dependencias.
     * No muestra información si la variable de ambiente PHP_ENV tiene el valor "production".
     *
     * @return void
     */
    private function setupRootPath(): void
    {
        $this->get('/', function ()
        {
            if (getenv('PHP_ENV') !== 'production') {
                $routes = $this->getRouter()->getRoutes();
                $routeDefinitions = [];

                foreach ($routes as $route) {
                    $method = $route->getHttpMethods();
                    $routeDefinitions[$method][] = $route->getPattern();
                }

                array_shift($routeDefinitions['OPTIONS']); // Se remueve la ruta del OptionsHandler.
                if (empty($routeDefinitions['OPTIONS'])) unset($routeDefinitions['OPTIONS']);

                return [
                    'routeDefinitions' => $routeDefinitions,
                    'services' => array_keys($this->di->getServices())
                ];
            }

            return [];
        });
    }

    /**
     * Manejador para atender las peticiones por OPTIONS.
     *
     * @return void
     */
    public function setupOptionsHandler(): void
    {
        $this->options('/{catch:(.*)}', function ()
        {
            $this->response->setContentType('application/json')->setStatusCode(200, 'OK');
        });
    }

    /**
     * Inicializa la configuración base de la aplicación.
     *
     * @return void
     */
    private function init(): void
    {
        $this->setDI($this->di);

        $this->setEventsManager($this->eventsManager);

        $this->setupRootPath();

        $this->setupOptionsHandler();

        $this->loadModule();
    }

    /**
     * Corre la aplicación y empieza a atender las peticiones.
     *
     * @return void
     */
    public function run(): void
    {
        $this->init();

        $this->handle(isset($_GET['_url']) ? $_GET['_url'] : '/');
    }

    /**
     * Corre la aplicación en modo de prueba (evitando la validación de Token del Servicio de Autenticación).
     *
     * @return void
     */
    public function runTestMode(): void
    {
        $this->isTestMode = true;

        $this->run();
    }

    /**
     * Corre la aplicación en modo de pruebas unitarias.
     *
     * @return void
     */
    public function runUnitTestMode(): void
    {
        $this->init();
    }
}
