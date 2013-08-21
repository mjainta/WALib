<?php
/**
 * Holds ViewManager.
 *
 * @package WALib
 */
namespace WALib\Mvc\Views;

use WALib\Application\Request as Request;
use WALib\Module\Module as Module;
/**
 * The view manager class used by the application and the instantiated controller.
 *
 * @package WALib
 */
class ViewManager
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $_request = null;

    /**
     * The module object for which the view should be displayed.
     *
     * @var Module
     */
    protected $_module = null;

    /**
     * The template file which will be included in the view process.
     *
     * Standard the template of the lib will be used.
     *
     * @var string
     */
    protected $_template = null;

    /**
     * An array with variables which can be accessed in the view.
     *
     * @var mixed[]
     */
    protected $_variables = array();

    /**
     * The JavaScript src for the current action view file.
     *
     * @var string
     */
    protected $_actionJavaScriptSrc = '';

    /**
     * Sets the request object.
     *
     * @param \WALib\Application\Request $request
     * @param \WALib\Module\Module $module
     */
    public function __construct(Request $request, Module $module)
    {
        $this->_request = $request;
        $this->_module = $module;
        $this->_variables = $this->_module->getViewVariables();

        if($module->getTemplate())
        {
            /*
             * Use the chosen template from the module, if one was set.
             */
            $this->setTemplate($module->getTemplate());
        }
        else
        {
            $this->setTemplate(LIB_PATH.'/Templates/index.phtml');
        }
    }

    /**
     * Renders the view.
     */
    public function render()
    {
        include_once $this->getTemplate();
    }

    /**
     * Renders the content of the view and its JavaScript file.
     *
     * Calls the .phtml and .js file corresponding to the module, controller and
     * action. If no corresponding files for the action exists, the files for the
     * index action will be called.
     * Should be called from the template file using §this->_renderContent().
     * The JavaScript file is not a requirement.
     */
    protected function _renderContent()
    {
        $viewFolder = APPLICATION_PATH.'/modules/'.$this->getRequest()->getModule().'/view/application/'.$this->getRequest()->getController();

        /*
         * The standard action view script. This file should always exist.
         * The JavaScript file has not to exists.
         */
        $viewScript = $viewFolder.'/index.phtml';
        $javaScriptFile = APPLICATION_PATH.'/public/js/modules/'.$this->getRequest()->getModule().'/'.$this->getRequest()->getController().'/index.js';

        /*
         * The view script for the current action. Does not have to exist.
         */
        $actionViewScript = $viewFolder.'/'.lcfirst($this->getRequest()->getAction()).'.phtml';
        $actionJavaScriptFile = APPLICATION_PATH.'/public/js/modules/'.$this->getRequest()->getModule().'/'.$this->getRequest()->getController().'/'.lcfirst($this->getRequest()->getAction()).'.js';

        if(is_file($actionViewScript))
        {
            /*
             * If the .phtml file for the action exists, we want to use the
             * JavaScript file for it, if it exists. If the JavaScript file does
             * not exist, we also dont want the JavaScript file of the index
             * action. In that case, no JavaScript file will be included.
             */
            $viewScript = $actionViewScript;
            $javaScriptFile = $actionJavaScriptFile;
        }

        include_once $viewScript;

        if(is_file($javaScriptFile))
        {
            /*
             * Generating the relative path to the javascript file.
             */
            $this->_actionJavaScriptSrc = str_replace(APPLICATION_PATH.'/public', '', $javaScriptFile);
        }
    }

    /**
     * Returns the JavaScript src for the current action view file.
     *
     * @return string
     */
    public function getActionJavaScriptSrc()
    {
        return $this->_actionJavaScriptSrc;
    }

    /**
     * Returns a helper instance of the requested object.
     *
     * @param string $name
     * @return object
     */
    public function getHelper($name)
    {
        $helperClass = $this->getRequest()->getModule().'\\Views\\Helper\\'.$name;

        /*
         * Checks if the helper is a helper of the current module.
         */
        if(!class_exists($helperClass))
        {
            /*
             * If not, the helper is from the WALib.
             */
            $helperClass = 'WALib\\Mvc\\Views\\Helper\\'.$name;
        }

        $helper = new $helperClass();

        return $helper;
    }

    /**
     * Returns a view variable if one with the given name exists.
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if(isset($this->_variables[$name]))
        {
            return $this->_variables[$name];
        }
    }

    /**
     * Adds a view variable.
     *
     * Overwrites existing variables with the same name.
     *
     * @param string $name
     * @param mixed $value
     */
    public function addVariable($name, $value)
    {
        $this->_variables[$name] = $value;
    }

    /**
     * Returns all view variables.
     *
     * @return mixed[]
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * Sets the view variables.
     *
     * @param mixed[] $variables
     */
    public function setVariables($variables)
    {
        $this->_variables = $variables;
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

    /**
     * Returns the module instance of the view to display.
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Sets the template to use for the view rendering process.
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }
}