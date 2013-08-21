<?php
/**
 * Holds Application.
 *
 * @package WALib
 */
namespace WALib\Application;

use WALib\Mvc\Views\ViewManager as ViewManager;
use WALib\Application\Request as Request;
use WALib\Application\AppConfig as AppConfig;
/**
 * Handles selection of controller and view and their data transfer.
 *
 * @package WALib
 */
class Application
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $_request = null;

    /**
     * Sets the config, request and the constant LIB_PATH for the path of the library.
     *
     * Also requires the functions.php script.
     *
     * @param mixed[] $config
     */
    public function __construct($config)
    {
        require_once __DIR__.'/../Scripts/functions.php';
        define('LIB_PATH', dirname(__DIR__));
        $this->_fetchConfig($config);
        AppConfig::add($config, false);
        $this->_request = new Request($config);
    }

    /**
     * Runs the application.
     *
     * Sets the requested module, controller and action. Then calls the
     * controller and gives it the view. After the action of the controller
     * is done the view will be rendered.
     *
     * @throws \InvalidArgumentException If the config is not right set or the
     * requested module doesnt exists.
     */
    public function run()
    {
        /*
         * Adding namespace/path pairs to the autoloader according to existing
         * modules in this application.
         */
        $autoloader = \WALib\Loader\Autoloader::getInstance();

        if(is_array(AppConfig::get('modules')))
        {
            foreach(AppConfig::get('modules') as $name)
            {
                $autoloader->addNamespace($name, APPLICATION_PATH.'/modules/'.$name.'/src');
            }
        }
        else
        {
            throw new \InvalidArgumentException('Config "modules" not set or not an array.');
        }

        /*
         * Setting the module, controller and action.
         */
        if(AppConfig::get('standardModule'))
        {
            $this->getRequest()->setModule(AppConfig::get('standardModule'));
        }

        /*
         * Initializing the controller, module and view and giving
         * the controller the view-object.
         */
        $moduleClass = $this->getRequest()->getModule().'\\Module';
        $module = new $moduleClass();
        $view = new ViewManager($this->getRequest(), $module);
        $controllerClass = $this->getRequest()->getModule().'\\Controllers\\'.$this->getRequest()->getController().'Controller';
        $controller = new $controllerClass($view);
        $controller->setRequest($this->getRequest());
        $actionMethod = $this->getRequest()->getAction().'Action';

        if(method_exists($controller, $actionMethod))
        {
            /*
             * Calling the controllers action.
             */
            $controller->$actionMethod();
        }
        else
        {
            $controller->indexAction();
        }

        $view->render();
    }

    /**
     * Fetches the config parameters from the config file to the AppConfig class.
     *
     * @param mixed[] $config
     */
    protected function _fetchConfig($config)
    {
        if(isset($config['databases']))
        {
            foreach($config['databases'] as $configKey => $dbParams)
            {
                $db = new \WALib\DB\MySQL($dbParams['host'], $dbParams['user'], $dbParams['password'], $dbParams['dbName']);
                AppConfig::set($configKey, $db);
            }

            unset($config['databases']);
            AppConfig::add($config);
        }

        if(isset($config['translator']))
        {
            AppConfig::set('translator', new \WALib\Translation\Translator($config['translator']));
        }
    }

    /**
     * Returns the request object.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
}