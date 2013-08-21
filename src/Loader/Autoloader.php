<?php
/**
 * Holds Autoloader.
 *
 * @package WALib
 */
namespace WALib\Loader;
/**
 * Holds the autoloader logic.
 *
 * Uses a singleton pattern using getInstance().
 *
 * @package WALib
 */
class Autoloader
{
    /**
     * The string used to separate namespaces.
     *
     * @var string
     */
    const NS_SEPARATOR = '\\';

    /**
     * An array of namespace/path pairs.
     *
     * @var mixed[]
     */
    protected $_namespaces = array();

    /**
     * An array with informations about external libraries.
     *
     * Key is the classname to search for, value the filename in which the
     * class will be found.
     *
     * @var mixed[]
     */
    protected $_externals = array();

    /**
     * The active instance of the autoloader.
     *
     * @var Autoloader
     */
    static private $_instance = null;

    /**
     * Returns the autoloader instance.
     *
     * @return Autoloader
     */
    static public function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new Autoloader();
        }

        return self::$_instance;
    }

    /**
     * Sets the standard namespace for the "WALib" folder.
     */
    public function __construct()
    {
        $this->addNamespace('WALib', dirname(__DIR__));
    }

    /**
     * Does the autoloading using namespaces.
     *
     * Uses the namespace/path pairs to generate the filepath to include.
     *
     * @param string $className
     * @return boolean
     */
    public function autoload($className)
    {
        $filename = $className;

        foreach($this->_namespaces as $namespace => $path)
        {
            /*
             * Replace found namespaces with the path and replace namespace
             * separators with a slash.
             */
            $filename = str_replace(array($namespace, self::NS_SEPARATOR), array($path, '/'), $filename);
        }

        $filename .= '.php';

        if(file_exists($filename))
        {
            require_once $filename;
        }
        elseif(isset($this->_externals[$className]))
        {
            require_once $this->_externals[$className];
        }
        else
        {
            return false;
        }
    }

    /**
     * Registers the autoloader for autoloading.
     */
    public function register()
    {
        spl_autoload_register
        (
            array
            (
                $this,
                'autoload'
            )
        );
    }

    /**
     * Adds a namespace/path pair for the autoloader.
     *
     * @param string $name
     * @param string $path
     */
    public function addNamespace($name, $path)
    {
        $this->_namespaces[$name] = $path;
    }

    /**
     * Adds an external library for the autoloader.
     *
     * @param string $classname Classname which will be required.
     * @param string $filename Filepath in which the class will be found.
     */
    public function addExternalWALib($classname, $filename)
    {
        $this->_externals[$classname] = $filename;
    }
}
