<?php
/**
 * Holds Request.
 *
 * @package WALib
 */
namespace WALib\Application;

/**
 * A class to represent the request variable and set presets.
 *
 * @package WALib
 */
class Request
{
    /**
     * Holds the module, controller and action request data.
     *
     * @var mixed[]
     */
    protected $_requestParameters = array
    (
        'module' => 'Index',
        'controller' => 'Index',
        'action' => 'Index'
    );

    /**
     * Holds request parameters.
     *
     * @var mixed[]
     */
    protected $_data = array();

    /**
     * The config for the application.
     *
     * @var mixed[]
     */
    protected $_config = array();

    /**
     * Extracts the data from the $_REQUEST and saves it in the object.
     */
    public function __construct($config)
    {
        $this->_config = $config;
        $this->_fetchRequest();

        /*
         * Setting the rest of the request data.
         */
        $this->_data = $_REQUEST;
    }

    protected function _fetchRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $baseURI = '';

        if(isset($this->_config['baseURI']))
        {
            $baseURI = $this->_config['baseURI'];
            $uri = preg_replace('/^\/?'.preg_quote($baseURI).'/', '', $uri);
        }

        /*
         * Only use the path of the uri for further processing to ignore
         * optional GET parameters delivered.
         */
        $uri = parse_url($uri, PHP_URL_PATH);

        /*
         * The URI path may start with a slash so remove it first.
         */
        $uri = trim($uri, '/');
        $uriParts = explode('/', $uri, 3);

        $paramDescriptors = array
        (
            0 => 'module',
            1 => 'controller',
            2 => 'action'
        );

        foreach($paramDescriptors as $uriKey => $requestKey)
        {
            if(!empty($uriParts[$uriKey]))
            {
                /*
                 * Setting the request parameter for module, controller and action.
                 */
                $this->_requestParameters[$requestKey] = $uriParts[$uriKey];
            }
        }
    }

    /**
     * Sets a key/value pair in the request data.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setDataValue($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Returns the value for a request data key or null, if it doesnt exists.
     *
     * @param mixed $key
     * @return mixed
     */
    public function getDataValue($key)
    {
        if(isset($this->_data[$key]))
        {
            return $this->_data[$key];
        }
        else
        {
            return null;
        }
    }

    /**
     * @return mixed[]
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed[] $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->_requestParameters['module'];
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->_requestParameters['module'] = $module;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->_requestParameters['controller'];
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->_requestParameters['controller'] = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->_requestParameters['action'];
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->_requestParameters['action'] = $action;
    }
}