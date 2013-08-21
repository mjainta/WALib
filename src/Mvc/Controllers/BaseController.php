<?php
/**
 * Holds BaseController.
 *
 * @package WALib
 */
namespace WALib\Mvc\Controllers;

use WALib\Mvc\Views\ViewManager as ViewManager;
use WALib\Application\Request as Request;
/**
 * Base class for controllers.
 *
 * Every controller for the app should extend from this class.
 *
 * @package WALib
 */
abstract class BaseController implements ControllerInterface
{
    /**
     * The view manager object.
     *
     * Used to perform data assignment to the view.
     *
     * @var ViewManager
     */
    protected $_view = null;

    /**
     * The request object.
     *
     * @var Request
     */
    protected $_request = null;

    /**
     * Sets the view manager.
     *
     * @param \WALib\Mvc\Views\ViewManager $view
     */
    public function __construct(ViewManager $view)
    {
        $this->_view = $view;
    }

    /**
     * Sets the action and controller parameters for the view request object.
     *
     * Using this function the action and controller of the request object
     * from the view will be modified.
     *
     * @param string $action
     * @param string $controller Dont modifies the controller if empty.
     */
    public function redirect($action, $controller = null)
    {
        $this->getView()->getRequest()->setAction($action);

        if($controller)
        {
            $this->getView()->getRequest()->setController($controller);
        }
    }

    /**
     * The standard action called if no action was given in the request.
     */
    abstract public function indexAction();

    /**
     * @return ViewManager
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * @param ViewManager $view
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * Returns the request object.
     *
     * @return \WALib\Application\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Sets the request object for the current action.
     *
     * @param \WALib\Application\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }
}